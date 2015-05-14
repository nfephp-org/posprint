# posprint
API para impressão em impressoras POS (Point of Sales) térmicas.

O modo de funcionamento desta API é pelo envio de comandos diretos (RAW) da impressora sem a necessidade de drivers especificos.
E esta construida com base nos comandos Esc/Pos disponíveis para as impressoras de vários fabricantes.
Com foco no mercado Brasileiro dessas impressoras.
Os fabricantes a seguir estão inclusos nessa API, porém nem todos os modelos dessas marcas irão funcionar devido a disponibilidade de recursos oferecidos pelo seu próprio firmware

EPSON (TM-T20)
DARUMA  (DR700)
BEMATECH (MP-4200 TH)
ELGIN (VOX)
STAR (BSC-10)
SWEDA (SI-300)
DIEBOLD (TSP143MD/MU)

Esta API deve prover acesso as impressoras térmicas conectadas atraves de :

Conexões Locais aos Servidor

Serial
USB
Cups
Windows Printer

Conexões Remotas

Cups
Windows Printer Server
IPP
LPR

Prerequisitos PHP

PHP > 5.3
ext-imagick



