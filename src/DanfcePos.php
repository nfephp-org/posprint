<?php

namespace Posprint;

/**
 * Esta classe foi colocada aqui apneas para facilitar o desenvolvimento, seu local correto
 * é no repositório sped-da
 *
 * em caso de contingência criar duas vias consumidor e estabelecimento
 */

use Posprint\Printers\PrinterInterface;
use InvalidArgumentException;

class DanfcePos
{
    /**
     * NFCe
     * @var SimpleXMLElement
     */
    protected $nfce = '';
    /**
     * protNFe
     * @var SimpleXMLElement
     */
    protected $protNFe = '';
    /**
     * Printer
     * @var PrinterInterface
     */
    protected $printer;
    /**
     * Documento montado
     * @var array
     */
    protected $da = array();
    /**
     * Total de itens da NFCe
     * @var integer
     */
    protected $totItens = 0;
    
    /**
     * URI referente a pagina de consulta da NFCe pela chave de acesso
     * @var string
     */
    protected $uri = '';
    
    protected $aURI = [
      'AC' => 'http://sefaznet.ac.gov.br/nfce/consulta.xhtml',
      'AM' => 'http://sistemas.sefaz.am.gov.br/nfceweb/formConsulta.do',
      'BA' => 'http://nfe.sefaz.ba.gov.br/servicos/nfce/Modulos/Geral/NFCEC_consulta_chave_acesso.aspx',
      'MT' => 'https://www.sefaz.mt.gov.br/nfce/consultanfce',
      'MA' => 'http://www.nfce.sefaz.ma.gov.br/portal/consultaNFe.do?method=preFilterCupom&',
      'PA' => 'https://appnfc.sefa.pa.gov.br/portal/view/consultas/nfce/consultanfce.seam',
      'PB' => 'https://www.receita.pb.gov.br/ser/servirtual/documentos-fiscais/nfc-e/consultar-nfc-e',
      'PR' => 'http://www.sped.fazenda.pr.gov.br/modules/conteudo/conteudo.php?conteudo=100',
      'RJ' => 'http://www4.fazenda.rj.gov.br/consultaDFe/paginas/consultaChaveAcesso.faces',
      'RS' => 'https://www.sefaz.rs.gov.br/NFE/NFE-COM.aspx',
      'RO' => 'http://www.nfce.sefin.ro.gov.br/home.jsp',
      'RR' => 'https://www.sefaz.rr.gov.br/nfce/servlet/wp_consulta_nfce',
      'SE' => 'http://www.nfce.se.gov.br/portal/portalNoticias.jsp?jsp=barra-menu/servicos/consultaDANFENFCe.htm',
      'SP' => 'https://www.nfce.fazenda.sp.gov.br/NFCeConsultaPublica/Paginas/ConsultaPublica.aspx'
    ];

    /**
     * Carrega a impressora a ser usada
     * a mesma deverá já ter sido pré definida inclusive seu
     * conector
     *
     * @param PrinterInterface $this->printer
     */
    public function __construct(PrinterInterface $printer)
    {
        $this->printer = $printer;
    }
    
    /**
     * Carrega a NFCe
     * @param string $nfcexml
     */
    public function loadNFCe($nfcexml)
    {
        $xml = $nfcexml;
        if (is_file($nfcexml)) {
            $xml = @file_get_contents($nfcexml);
        }
        if (empty($xml)) {
            throw new InvalidArgumentException('Não foi possivel ler o documento.');
        }
        $nfe = simplexml_load_string($xml, null, LIBXML_NOCDATA);
        $this->protNFe = $nfe->protNFe;
        $this->nfce = $nfe->NFe;
        if (empty($this->protNFe)) {
            //NFe sem protocolo
            $this->nfce = $nfe;
        }
    }
    
    /**
     * Monta a DANFCE para uso de impressoras POS
     */
    public function monta()
    {
        $this->parteI();
        $this->parteII();
        $this->parteIII();
        $this->parteIV();
        $this->parteV();
        $this->parteVI();
        $this->parteVII();
        $this->parteVIII();
        $this->parteIX();
    }
    
    /**
     * Manda os dados para a impressora ou
     * retorna os comandos em ordem e legiveis
     * para a tela
     */
    public function printDanfe()
    {
        $resp = $this->printer->send();
        if (!empty($resp)) {
            echo str_replace("\n", "<br>", $resp);
        }
    }
    
    /**
     * Recupera a sequiencia de comandos para envio
     * posterior para a impressora por outro
     * meio como o QZ.io (tray)
     *
     * @return string
     */
    public function getCommands()
    {
        $aCmds = $this->printer->getBuffer('binA');
        return implode("\n", $aCmds);
    }
    
    /**
     * Parte I - Emitente
     * Dados do emitente
     * Campo Obrigatório
     */
    protected function parteI()
    {
        $razao = (string) $this->nfce->infNFe->emit->xNome;
        $cnpj = (string) $this->nfce->infNFe->emit->CNPJ;
        $ie = (string) $this->nfce->infNFe->emit->IE;
        $im = (string) $this->nfce->infNFe->emit->IM;
        $log = (string) $this->nfce->infNFe->emit->enderEmit->xLgr;
        $nro = (string) $this->nfce->infNFe->emit->enderEmit->nro;
        $bairro = (string) $this->nfce->infNFe->emit->enderEmit->xBairro;
        $mun = (string) $this->nfce->infNFe->emit->enderEmit->xMun;
        $uf = (string) $this->nfce->infNFe->emit->enderEmit->UF;
        if (array_key_exists($uf, $this->aURI)) {
            $this->uri = $this->aURI[$uf];
        }
        $this->printer->setAlign('C');
        $this->printer->setBold();
        $this->printer->text($razao);
        $this->printer->setBold();
        $this->printer->lineFeed();
        $this->printer->text('CNPJ: '.$cnpj.'     '.'IE: ' . $ie);
        $this->printer->lineFeed();
        $this->printer->text($log . ', ' . $nro);
        $this->printer->lineFeed();
        $this->printer->text($bairro . ', ' . $mun . ' - ' . $uf);
        $this->printer->lineFeed();
    }
    
    /**
     * Parte II - Informações Gerais
     * Campo Obrigatório
     */
    protected function parteII()
    {
        $this->printer->setAlign('C');
        $this->printer->text('DANFE NFC-E Nota Fiscal Eletronica');
        $this->printer->lineFeed();
        $this->printer->text('para Consumidor Final');
        $this->printer->lineFeed();
        $this->printer->setBold();
        $this->printer->text('Nao permite aproveitamento de credito de ICMS');
        $this->printer->setBold();
        $this->printer->lineFeed();
    }
    
    /**
     * Parte III - Detalhes da Venda
     * Campo Opcional
     */
    protected function parteIII()
    {
        $this->printer->setAlign('L');
        //obter dados dos itens da NFCe
        $det = $this->nfce->infNFe->det;
        $this->totItens = $det->count();
        $vtot = 0;
        for ($x=0; $x<=$this->totItens-1; $x++) {
            $nItem = (int) $det[$x]->attributes()->{'nItem'};
            $cProd = (string) $det[$x]->prod->cProd;
            $xProd = (string) $det[$x]->prod->xProd;
            $qCom = (float) $det[$x]->prod->qCom;
            $uCom = (string) $det[$x]->prod->uCom;
            $vUnCom = (float) $det[$x]->prod->vUnCom;
            $vProd = (float) $det[$x]->prod->vProd;
            $vtot += $vProd;
            $this->printer->setBold();
            $this->printer->text($cProd . " - " . $xProd);
            $this->printer->setBold();
            $this->printer->lineFeed();
            $this->printer->setUnderlined();
            $printvalue = str_pad((string) $qCom." ".$uCom."   *  ".$vUnCom." = ",40," ",STR_PAD_LEFT) . str_pad((string) $vProd,10," ",STR_PAD_LEFT);
            $this->printer->text($printvalue);
            $this->printer->setUnderlined();
            $this->printer->lineFeed();
        }
        $printtot = str_pad((string) "Total:",40," ",STR_PAD_LEFT) . str_pad((string) $vtot,10," ",STR_PAD_LEFT);
        $this->printer->setBold();
        $this->printer->text($printtot);
        $this->printer->setBold();
        $this->printer->lineFeed();
    }

    /**
     * Parte IV - Totais da Venda
     * Campo Obrigatório
     */
    protected function parteIV()
    {
        $vNF = (float) $this->nfce->infNFe->total->ICMSTot->vNF;
        $this->printer->setAlign('L');
        $printTotItens = str_pad((string) 'QTD. TOTAL DE ITENS:',40," ",STR_PAD_LEFT) . str_pad((string) $this->totItens,10," ",STR_PAD_LEFT);
        $this->printer->text($printTotItens);
        $this->printer->lineFeed();
        $pag = $this->nfce->infNFe->pag->detPag;
        $tot = $pag->count();
        for ($x=0; $x<=$tot-1; $x++) {
            $tPag = (string) $pag->tPag;
            $tPag = (string) $this->tipoPag($tPag);
            $vPag = (float) $pag->vPag;
            $printFormPag = str_pad((string) $tPag,40," ",STR_PAD_LEFT) . str_pad((string) $vPag,10," ",STR_PAD_LEFT);
            $this->printer->text($printFormPag);
            $this->printer->lineFeed();
        }
    }
    
    /**
     * Parte V - Informação de tributos
     * Campo Obrigatório
     */
    protected function parteV()
    {
        $vTotTrib = (float) $this->nfce->infNFe->total->ICMSTot->vTotTrib;
        $this->printer->setAlign('L');
        //$this->printer->text('Informação dos Tributos Totais:' . '' . 'R$ ' .  $vTotTrib);
        $printimp = str_pad((string) "Informacao dos Tributos Incidentes:",40," ",STR_PAD_LEFT) . str_pad((string) $vTotTrib,10," ",STR_PAD_LEFT);
        $this->printer->text($printimp);
        $this->printer->lineFeed();
        $this->printer->setAlign('C');
        $this->printer->text('Incidentes (Lei Federal 12.741 /2012) - Fonte IBPT');
        $this->printer->lineFeed();
    }
    
    
    /**
     * Parte VI - Mensagem de Interesse do Contribuinte
     * conteudo de infCpl
     * Campo Opcional
     */
    protected function parteVI()
    {
        $infCpl = (string) $this->nfce->infNFe->infAdic->infCpl;
        $this->printer->setAlign('L');
        $this->printer->text($infCpl);
        $this->printer->lineFeed();
        //linha divisória ??
    }
    
    /**
     * Parte VII - Mensagem Fiscal e Informações da Consulta via Chave de Acesso
     * Campo Obrigatório
     */
    protected function parteVII()
    {
        $tpAmb = (int) $this->nfce->infNFe->ide->tpAmb;
        if ($tpAmb == 2) {
            $this->printer->setAlign('C');
            $this->printer->text('EMITIDA EM AMBIENTE DE HOMOLOGAÇÃO - SEM VALOR FISCAL');
            $this->printer->lineFeed();
        }
        $tpEmis = (int) $this->nfce->infNFe->ide->tpEmis;
        if ($tpEmis != 1) {
            $this->printer->setAlign('C');
            $this->printer->text('EMITIDA EM AMBIENTE DE CONTINGẼNCIA');
            $this->printer->lineFeed();
        }
        $nNF = (float) $this->nfce->infNFe->ide->nNF;
        $serie = (int) $this->nfce->infNFe->ide->serie;
        $dhEmi = (string) $this->nfce->infNFe->ide->dhEmi;
        $Id = (string) $this->nfce->infNFe->attributes()->{'Id'};
        $chave = substr($Id, 3, strlen($Id)-3);
        $this->printer->setAlign('C');
        //$this->printer->text('Nr. ' . $nNF. ' Serie ' .$serie . ' Emissão ' .$dhEmi . ' via Consumidor');
        $this->printer->text("NFCE: ". preg_replace( "/[^0-9]/", "", $nNF ) ."   Serie: ". $serie ."   ". date( 'd/m/Y H:i:s', strtotime($dhEmi) ) );
        $this->printer->lineFeed();
        $this->printer->text('Consulte pela chave de acesso em');
        $this->printer->lineFeed();
        $this->printer->text($this->uri);
        $this->printer->lineFeed();
        $this->printer->text('CHAVE DE ACESSO');
        $this->printer->lineFeed();
        $this->printer->text($chave);
        $this->printer->lineFeed();
    }
    
    /**
     * Parte VIII - Informações sobre o Consumidor
     * Campo Opcional
     */
    protected function parteVIII()
    {
        $this->printer->setAlign('C');
        $dest = $this->nfce->infNFe->dest;
        if (empty($dest)) {
            $this->printer->setBold();
            $this->printer->text('CONSUMIDOR NAO IDENTIFICADO');
            $this->printer->setBold();
            $this->printer->lineFeed();
            return;
        }
        $xNome = (string) $this->nfce->infNFe->dest->xNome;
        $this->printer->text($xNome);
        $this->printer->lineFeed();
        $cnpj = (string) $this->nfce->infNFe->dest->CNPJ;
        $cpf = (string) $this->nfce->infNFe->dest->CPF;
        $idEstrangeiro = (string) $this->nfce->infNFe->dest->idEstrangeiro;
        $this->printer->setAlign('C');
        if (!empty($cnpj)) {
            $this->printer->text('CNPJ ' . $cnpj);
            $this->printer->lineFeed();
        }
        if (!empty($cpf)) {
            $this->printer->text('CPF ' . $cpf);
            $this->printer->lineFeed();
        }
        if (!empty($idEstrangeiro)) {
            $this->printer->text('Extrangeiro ' . $idEstrangeiro);
            $this->printer->lineFeed();
        }
        $xLgr = (string) $this->nfce->infNFe->dest->enderDest->xLgr;
        $nro = (string) $this->nfce->infNFe->dest->enderDest->nro;
        $xCpl = (string) $this->nfce->infNFe->dest->enderDest->xCpl;
        $xBairro = (string) $this->nfce->infNFe->dest->enderDest->xBairro;
        $xMun = (string) $this->nfce->infNFe->dest->enderDest->xMun;
        $uf = (string) $this->nfce->infNFe->dest->enderDest->UF;
        $cep = (string) $this->nfce->infNFe->dest->enderDest->CEP;
        $this->printer->text($xLgr . ', ' . $nro . ', ' . $xCpl . ', ' . $xBairro);
        $this->printer->lineFeed();
        $this->printer->text($xMun . ' - ' . $uf);
        $this->printer->lineFeed();
    }
    
    /**
     * Parte IX - QRCode
     * Consulte via Leitor de QRCode
     * Protocolo de autorização 1234567891234567 22/06/2016 14:43:51
     * Campo Obrigatório
     */
    protected function parteIX()
    {
        $this->printer->setAlign('C');
        $this->printer->text('Consulte via Leitor de QRCode');
        $this->printer->lineFeed();
        $qr = (string) $this->nfce->infNFeSupl->qrCode;
        $this->printer->barcodeQRCode($qr);
        if (!empty($this->protNFe)) {
            $nProt = (string) $this->protNFe->infProt->nProt;
            $dhRecbto = (string) $this->protNFe->infProt->dhRecbto;
            $this->printer->text('Protocolo de autorização ' . $nProt);
            $this->printer->lineFeed();
        } else {
            $this->printer->setBold();
            $this->printer->text('NOTA FISCAL INVÁLIDA - SEM PROTOCOLO DE AUTORIZAÇÃO');
            $this->printer->lineFeed();
        }
    }
    
    /**
     * Retorna o texto referente ao tipo de pagamento efetuado
     * @param int $tPag
     * @return string
     */
    private function tipoPag($tPag)
    {
        $aPag = [
            '01' => 'Dinheiro',
            '02' => 'Cheque',
            '03' => 'Cartao de Credito',
            '04' => 'Cartao de Debito',
            '05' => 'Credito Loja',
            '10' => 'Vale Alimentacao',
            '11' => 'Vale Refeicao',
            '12' => 'Vale Presente',
            '13' => 'Vale Combustivel',
            '99' => 'Outros'
        ];
        if (array_key_exists($tPag, $aPag)) {
            return $aPag[$tPag];
        }
        return '';
    }
}
