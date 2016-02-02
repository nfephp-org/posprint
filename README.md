# posprint
API para impressão em impressoras termicas POS (Point of Sales). Usadas em caixa de redes varejistas.

## ATENÇÃO ESTE PACOTE AINDA ESTÁ EM FASE ALPHA E NÃO É FUNCIONAL.
### Necessitamos de colaboradores !

O modo de funcionamento desta API é pelo envio de comandos diretos (RAW) para a impressora, sem a necessidade de uso de drivers especificos.
E está construída com base nos comandos Esc/Pos disponíveis para as impressoras de vários fabricantes (marcas e modelos escolhidos com foco no mercado Brasileiro dessas impressoras).

Os fabricantes a seguir estão inclusos nessa API, porém nem todos os modelos dessas marcas irão funcionar devido a disponibilidade de recursos oferecidos pelo seu próprio firmware. (Os modelos indicados por enquanto são mera informação de base, ainda não foram feitos todos os testes funcionais reais).

* EPSON (TM-T20)
* DARUMA  (DR700)
* BEMATECH (MP-4200 TH)
* ELGIN (VOX)
* STAR (BSC-10)
* SWEDA (SI-300)
* DIEBOLD (TSP143MD/MU)

Esta API deve prover acesso as impressoras térmicas conectadas atraves de várias formas de conexão e em qualquer sistema operacional, dependendo apenas da correta intalação e configuração da conexão, sem a necessidade de drivers especificos para cada S.O. (apenas um driver RAW padrão em alguns casos) :

* Serial
* Paralela
* USB
* Cups Print Server
* Windows Printer Server
* IPP
* LPR
* Web (jZebra java applet)

Prerequisitos PHP

* PHP > 5.3
* ext-imagick (para tratamento das imagens e logos)

Referencias 
* [escpos-php](https://github.com/mike42/escpos-php)
* [php-esc-pos](https://github.com/ronisaha/php-esc-pos)
* [PHP:IPP](http://www.nongnu.org/phpprintipp/) (C) Thomas Harding
