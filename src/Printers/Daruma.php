<?php

namespace Posprint\Printers;

/**
 * Classe Daruma das impressoras POS.
 * 
 * Foi construída em torno dos comandos da DR700
 * Velocidade de Impressão: 150 mm/s (E), 200 mm/s (ETH) ou 300 mm/s (HE)
 * Resolução: 180 dpi
 * Papel:  57mm, 76mm, 80mm e 82,5 mm 
 * Largura de Impressão: Papel 80 mm (Max. 72 mm) (576 pontos) ou (Max. 78 mm) (624 pontos)
 * Colunas: Normal - 52 ou 48, Elite - 44 ou 40 ou Condensado - 62 ou 57
 * Largura de Impressão: Papel 58 mm (Máx. 54 mm)
 * 
 * NOTA: QR Code (firmware a partir de 2.50.00)
 * 
 * @category   NFePHP
 * @package    Posprint
 * @copyright  Copyright (c) 2015
 * @license    http://www.gnu.org/licenses/lesser.html LGPL v3
 * @author     Roberto L. Machado <linux.rlm at gmail dot com>
 * @link       http://github.com/nfephp-org/posprint for the canonical source repository
 */

use Posprint\Printers\Basic\Printer;
use Posprint\Printers\Basic\PrinterInterface;


class Daruma extends Printer implements PrinterInterface
{
    public $aCountry = array('LATIN');
    
    public $aCodePage = array(    
        'ISO8859-1' => array('conv'=>'ISO8859-1','table'=>'0','desc'=>'ISO8859-1: Latin'),
        'CP850' => array('conv'=>'850','table'=>'1','desc'=>'PC850: Multilingual'),
        'ABICOMP' => array('conv'=>'','table'=>'2','desc'=>'ABICOMP'),
        'CP437' => array('conv'=>'437','table'=>'3','desc'=>'CP437')
    );

    
    /**
     * setPrinterMode
     * Seta o modo de impressão 
     * @param type $printerMode
     */
    public function setPrinterMode()
    {
        //[ESC] 198
        //  0
        //  XXXX
        //  n   zero cortado 0 = desligado, 1 - ligado
        //  n   Desabilita Teclado
        //  n   Guilhotina Habilitada
        //  n   Tipo do corte da guilhotina 0 = Total, 1 = Parcial
        //  X
        //  n   Numero de colunas 0 = 48 col, 1 = 52 col* 2 = 34 col**
        //  XXX
        //  n   Baudrate
        //  n   Controle de fluxo 0 = RTS/CTS, 1 = XON/XOFF
        //  XXXXXXXXXXXXXXXXX
        //  nn  00 a 20 = linhas de acionamento antes do corte da guilhotina
        //  n   1/2 = Tabela de comandos 1 ou 2
        //  n   Interchar delay (ms)
        //  XX
        //  n   CodePage
        $zerocortado = 0;
        $disablekeyboard = 0;
        $enablecutpartial = 0;
        $numcols = 0; //48colunas
        $baudrate = 8;
        //Baud Rate: 1 = 1200 2 = 2400 3 = 230400 (USB) 4 = 4800 5 = 57600
        //6 = 19200 7 = 38400 8 = 115200 (default para USB) 9 = 9600 (default para COM)
        $flowcontrol = 0; //0 = RTS/CTS, 1 = XON/XOFF
        $commandtable = 1; //1/2 = Tabela de comandos 1 ou 2
        $interchardelay = 0;
        $codepage = 0;
        $this->text(
            self::ESC
            . chr(198)
            . chr(0)
            . 'XXXX'
            . chr($zerocortado)
            . chr($disablekeyboard)
            . chr($enablecutpartial)
            . 'X'
            . chr($numcols)
            . 'XXX'
            . chr($baudrate = 8)
            . chr($flowcontrol)
            . 'XXXXXXXXXXXXXXXXX'
            . chr($commandtable)
            . chr($interchardelay)
            . 'XX'
            . chr($codepage),
            self::NOTRANS
        );
    }

    
    
    //[ESC] 2 <32> 0 Espaçamento entre linhas padrão
    //Espaçamento entre linhas padrão
    //      ESC 2
    //Seleciona espaçamento de linha padrão de 1/8"
    
    //[ESC] 3 <33> 1 Configura espaçamento entre linhas
    //  Configura o espaçamento entre linhas
    // ESC 3 n
    //Seleciona espaçamento de linha de:
    //  Modo1 - n x Unidade de Movimento Vertical
    //  Modo2 - n/200"    
    
    //[ESC] 4 <34> 1 Modo Itálico
    //  Modo Itálico de Impressão*
    //  ESC 4 n
    //      n = 0 – desliga (default)
    //      n = 1 – liga
    //  * Apenas para V.02.20.00 ou superior.
    
    //[ESC] @ <40> 0 Reinicia a impressora
    
    //[ESC] B <42> 16 Programa tabulações verticais
    //Programa tabulações verticais
    //ESC B n1 n2 ... nk NULL
    //      0 ≤ k ≤ 16
    //      1 ≤ n ≤ 127
    //      nk > n(k-1)
    //ESC B NULL
    //  Anula programação da tabulação vertical
      
    //[ESC] C <43> 1 Programa tamanho da página
    //Programa o tamanho da página
    //ESC C n
    //  Programa o tamanho da página em linhas
    //  O default é de 66 linhas.
    //  1 ≤ n ≤ 127
      
    //[ESC] D <44> 8 Programa tabulações horizontais
    //Programa tabulações horizontais
    //ESC D n1 ... nk NULL
    //  O default é a cada 8 colunas.
    //  A tabulação é deslocada de acordo com a margem esquerda
    //  1 ≤ n ≤ 8
    //ESC D NULL
    //  Anula programação da tabulação horizontal

    //[ESC] E <45> 0 Inicia negrito
    //[ESC] G <47> 0 Inicia negrito ( idem a [ESC] E )
    //[ESC] F <46> 0 Encerra negrito
    //[ESC] H <48> 0 Encerra negrito ( idem a [ESC] F )
    
    //[ESC] J <4D> 1 Imprime e avança papel
    //  Imprime e avança papel
    //      ESC J n
    //  Causa a impressão do que está no buffer e avança o papel de:
    //  Modo 1 - n x Unidade de Movimento Vertical
    //  Modo 2 - n x 0,125 mm
            
    //[ESC] Q <51> 1 Programa margem direita
    //Programa margem direita
    //ESC Q n
    //Programa margem direita em colunas de acordo com o tamanho da fonte vigente
    //no momento do comando
    //  3 ≤ n ≤ 48
    //  (margem direita) > (margem esquerda-2)

    //[ESC] R <52> 0 Re-inicia a impressora ( idem a [ESC] @ ) |
    //[ESC] W <57> 1 Liga/desliga modo expandido
    //[ESC] [SO] <0E> 0 Inicia dupla largura por uma linha
    //[ESC] [SI] <0F> 0 Seleciona modo condensado
    //[ESC] [DC4] <14> 0 Cancela dupla largura por 1 linha
    
    //[ESC] ! <21> 1 Programa o modo da impressora
    //Programa o modo da impressora
    //ESC ! n
    //    n (BIT)       FUNÇÃO
    //    0 ..... 0     fonte normal
    //            1     fonte elite
    //    3 ..... 0     desliga enfatizado
    //            1     liga enfatizado
    //    4 ..... 0     desliga dupla altura
    //            1     liga dupla altura
    //    5 ..... 0     desliga expandido
    //            1     liga expandido
    //    7 ..... 0     desliga sublinhado
    //            1     liga sublinhado

    //[ESC] # <23> 1 Impressões especiais*
    //[ESC] - <2D> 1 Ativa/desativa modo sublinhado
    //[ESC] f <66> 2 Deslocamento horizontal/vertical
    
    //[ESC] j <6A> 1 Justificação de texto
    //  Justificação de Texto*
    //  ESC j n
    //      n = 0 – à esquerda (default)
    //      n = 1 – centralizada
    //      n = 2 – à direita
    //  OBS: O comando de justificação de texto desliga as configurações de margem.
    //* Apenas para V.02.20.00 ou superior.
    
    //[ESC] l <6C> 1 Programa a margem esquerda
    //[ESC] m <6d> 0 Aciona guilhotina
    //[ESC] p <70> 0 Abre a gaveta
    //[ESC] w <77> 1 Liga/desliga modo dupla altura
    //[ESC] a <61> n Imprime código de barras vertical
    
    //[ESC] b <62> n Imprime código de barras horizontal
    //Imprime código de barras horizontal
    //ESC b n1 n2 n3 n4 s1...sn NULL
    //n1 – tipo do código a ser impresso
    //EAN13    1
    //EAN8    2
    //S2OF5    3
    //I2OF5    4
    //CODE128   5
    //CODE39    6
    //CODE93    7
    //UPC_A    8
    //CODABAR   9
    //MSI    10
    //CODE11    11
    //  n2 – largura da barra. De 2 a 5. Se 0, é usado 2.
    //  n3 – altura da barra. De 50 a 200. Se 0, é usado 50.
    //  n4 – se 1, imprime o código abaixo das barras
    //  s1...sn – string contendo o código.
    //  EAN-13: 12 dígitos de 0 a 9
    //  EAN–8: 7 dígitos de 0 a 9
    //  UPC–A: 11 dígitos de 0 a 9
    //CODE 39 : Tamanho variável. 0-9, A-Z, '-', '.', '%', '/', '$', ' ', '+'
    //O caracter '*' de start/stop é inserido automaticamente.
    //Sem dígito de verificação MOD 43
    //CODE 93: Tamanho variável. 0-9, A-Z, '-', '.', ' ', '$', '/', '+', '%'
    //O caracter '*' de start/stop é inserido automaticamente.
    //CODABAR: tamanho variável. 0 - 9, '$', '-', ':', '/', '.', '+'
    //Existem 4 diferentes caracteres de start/stop: A, B, C, and D que são
    //usados em pares e não podem aparecer em nenhum outro lugar do código.
    //Sem dígito de verificação
    //CODE 11: Tamanho variável. 0 a 9
    //Checksum de dois caracteres.
    //CODE 128: Tamanho variável. Todos os caracteres ASCII.
    //Interleaved 2 of 5: tamanho sempre par. 0 a 9. Sem dígito de verificação
    //Standard 2 of 5 (Industrial): 0 a 9. Sem dígito de verificação
    //MSI/Plessey: tamanho variável. 0 - 9. 1 dígito de verificação
    //n ≤ 25
    //Resposta:
    //  : E NN [CR]
    //  Valores de NN:
    //      00 sem erro
    //      01 string possui caracter inválido
    //      02 string possui tamanho inválido
    //      99 tipo de código inexistente
    //
    //** Dependendo do tamanho de n2 e de sn, alguns códigos poderão extrapolar a
    //largura do papel e serão truncados pela impressora, não sendo possível a
    //leitura posterior. Os códigos mais eficientes e que aceitam com largura 2
    //o tamanho de 25 caracteres são o CODE11, CODE128, CODABAR e I25

    
    
    
    //[ESC] 128 <80> n Imprime PDF417
    //[ESC] 129 <81> n Imprime QR Code
    //[ESC] 195 <C3> 0 Informa identificação da impressora
    //[ESC] 197 <C5> 1 Imprime caracteres especiais
    
    //[ESC] 198 <C6> 40 Configura impressora dinamicamente
    //  Configuração da impressora (modo dinâmico)
    //  [ESC] 198 0XXXX567890XXX4XXXXXXXXXXXXXXXXXX3456XX9
    //  Os bytes de controle são os mesmos do comando ESC 228. A diferença é que as
    //  configurações desse comando não são armazenadas na memória flash. A impressora volta
    //  com as configurações armazenadas quando desligada e ligada novamente.
    //  Obs: comando sem resposta    
    
    
    
    //[ESC] 199 <C7> 0 Informa a versão do FW
    
    //[ESC] 228 <E4> 40 Configura impressora
    //O comando ESC 228 armazena suas configurações em memória flash, que é uma memória do
    //tipo não-volátil. Ou seja, os valores configurados não são perdidos após o
    //desligamento da impressora. Durante esse processo de atualização da memória flash,
    //que dura aproximadamente um décimo de segundo, a impressora fica impossibilitada de
    //receber novos dados pela interface de comunicação. Dessa maneira, após o envio do
    //comando ESC 228 é imperativo que se aguarde sua resposta antes do envio de novos
    //dados.
    //Caso deseje-se alterar as configurações da impressora dinamicamente deve ser
    //utilizado o comando ESC 198, cujas configurações não são armazenadas na flash.
    //
    //OBS: Os valores de tabulações vertical e horizontal, margens esquerda e direita e de
    //tamanho de página, não ficam armazenado em memória flash e sempre são configurados
    //com seus valores default ao se ligar a impressora.
        
    //[ESC] 229 <E5> 0 Lê configuração da impressora
    //[ESC] 230 <E6> 0 Lê o relógio da impressora*
    
    //[GS][ENQ] <1D> 0 Solicitação de status 2
    //Palavra de Status 2
    //[GS][ENQ]
    //  (BIT)     FUNÇÃO
    //    0 ..... 0 – Papel não acabando
    //            1 – Pouco papel
    //    1 ..... 0 – Papel OK
    //            1 – Fim de papel
    //    2 ..... 0 –
    //            1 – Sempre 1
    //    3 ..... 0 – On Line
    //            1 – Off Line
    //    4 ..... 0 – Sem papel sobre o sensor
    //            1 – Papel posicionado sobre o sensor
    //    5 ..... 0 – Sempre 0
    //            1 –
    //    6 ..... 0 – Impressora operacional
    //            1 – Impressora em falha
    //    7 ..... 0 – Gaveta fechada
    //            1 – Gaveta aberta
    
    //[FS] M 200 <C8> 14 Ajusta o relógio*
    //[FS] M 209 <D1> n Carrega logotipo
    
    //[FS] M 254 <FE> 0 Imprime valores das margens e tabulações
    //  Imprime valores configurados para margens e tabulações
    //  [FS] M <254> <CS>
    //  Resposta:
    //      : NNNNN WW <2549> [CR] <CS>
    //  Obs: utilizado para auxiliar no desenvolvimento de aplicativos
    
    //[DLE] A <10> 2 Configura unidade de movimento
    //Configura a unidade de movimento horizontal e vertical
    //DLE A x y
    //  Ajusta a unidade de movimento horizontal e vertical para aproximadamente
    //  25.4/x mm {1/x"} e 25.4/y mm {1/y"}. A unidade horizontal (x) não é utilizada
    //  na impressora.
    //  Faixa:
    //    0 ≤ x ≤ 255
    //    0 ≤ y ≤ 255
    //    Padrão: x = 200 (sem uso na impressora)
    //            y = 400
    //Quando x e y são igual a zero, o valor padrão é carregado.
        
    //Reversão do Motor*
    //DLE M n
    //  n – número de linha a recurar: 1 ≤ x ≤ 15
    //IMPORTANTE: Não reverter o motor após um corte de guilhotina pois causará
    //embolamento do papel no rolo de tração.
    //* Apenas para V.02.50.00 ou superior.
    
    //ESC * m n1 n2 d1...dk
    //  (1BH 2AH m n1 n2 d1...dk)
    //  Imprime gráficos de 8 ou 24 bits
    //      horiz/   vert/   dots/   dots/
    //   m  dpi      dpi     col     col
    //   0  100      67      8      1 -> k = n1 + n2 × 256
    //   1  200      67      8      1
    //   32 100         200     24     3 -> k = (n1 + n2 × 256) × 3
    //   33 200      200     24     3
    //Obs.: 1. Compatível com EPSON e BEMATECH
    //2. Imprime gráficos linha a linha. Evitar utilizar para grandes imagens

    
    
    //DLE X m xL xH yL yH d1....dk
    //(10H 58H m xL xH yL yH d1...dk)
    //  Imprime uma imagem do tipo raster
    //0 ≤ m ≤ 3
    //0 ≤ xL ≤ 255
    //0 ≤ xH ≤ 255
    //0 ≤ yL ≤ 255
    //0 ≤ yH ≤ 255
    //0 ≤ d ≤ 255
    //k = (xL + xH +256) x (yl + yH * 256) (k != 0)
    //xL, xH -> número de bytes de dados na direção horizontal
    //yL, yH -> número de bytes de dados na direção vertical
    //m Mode         Vertical Dot Density   Horizontal Dot Density
    //0 Normal          200 dpi                 200 dpi
    //1 Double-width    200 dpi                 100 dpi
    //2 Double-height   100 dpi                 200 dpi
    //3 Quadruple    100 dpi                 100 dpi
    //Obs.: 1. Utilizar esse comando para imagens grandes ou pequenas. O limite
    //de tamanho para k é de 8KB.
    //2. A imagem raster é uma imagem que vem varrida de cima para baixo e da
    //esquerda pra direita, Cada linha varrida compõe o padrão que deve ser
    //enviado para a impressora.
    
    
    //Sincronismo do logotipo
    //SYN 8 Sinaliza início do logotipo
    //SYN 9 Sinaliza final do logotipo

}
