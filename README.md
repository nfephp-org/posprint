[![Build Status](https://travis-ci.org/nfephp-org/posprint.svg?branch=master)](https://travis-ci.org/nfephp-org/posprint)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/nfephp-org/posprint/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/nfephp-org/posprint/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/nfephp-org/posprint/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/nfephp-org/posprint/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/nfephp-org/posprint/v/stable)](https://packagist.org/packages/nfephp-org/posprint)
[![Total Downloads](https://poser.pugx.org/nfephp-org/posprint/downloads)](https://packagist.org/packages/nfephp-org/posprint)
[![Latest Unstable Version](https://poser.pugx.org/nfephp-org/posprint/v/unstable)](https://packagist.org/packages/nfephp-org/posprint)
[![License](https://poser.pugx.org/nfephp-org/posprint/license)](https://packagist.org/packages/nfephp-org/posprint)

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
* Web (qz.io java)

Prerequisitos PHP

* PHP >= 5.6
* ext-gd (para tratamento das imagens e logos) 
* estudando a possibilidade de uso de ext-imagick 

Referencias 
* [escpos-php](https://github.com/mike42/escpos-php)
* [php-esc-pos](https://github.com/ronisaha/php-esc-pos)
* [PHP:IPP](http://www.nongnu.org/phpprintipp/) (C) Thomas Harding


## Funcionamento básico

- Carrega classe do conector apropriado
- Carrega classe da impressora apropriada
- Envia a sequencia de comandos usando as funções básicas da interface e da classe de impressora
- Envia os comandos para a impressora usando o conector escolhido


## NOTAS DOS COLABORADORES

O problema que encontramos, foi na classe PhpSerial, pois o autor resolveu executar os comandos do windows para alteração de porta separadamente. Porem no windows, toda vez que o comando "mode PORTA" é executado, se passado algum parametro, os outros voltam ao default. E a impressora deve funcionar com a porta da forma que está na imagem abaixo.

![Alt CMD](images/wincmd.png?raw=true "CMD")
​
Para isso acontecer voce deve alterar as linhas do arquivo posprint-master/vendor/hyperthese/php-serial/src/PhpSerial.php

```php
    //$this->_device = "\\.com" . $matches[1];
    $this->_device = "COM" . $matches[1];
```

Foi alterado essa linha pois o windows não abre porta serial com o comando "\\.com" e sim "COM"

Depois fizemos outra alteração que é na linha abaixo.

```php
 "mode " . $this->_winDevice . " PARITY=" . $parity{0},
 "mode " . $this->_winDevice . " DATA=8 PARITY=" . $parity{0},
```
A alteração foi necessária devido a explicação feita no inicio do e-mail.

Pessoal, essas alterações foram feitas apenas para testes no windows utilizando a porta serial, lembrando que esse não é o intuito do projeto.

Atenciosamente, 

R Ribeiro Soares

## Instalação (*Install*)
Via Composer

``` bash
$ composer require nfephp-org/posprint
```

## Exemplo de Uso  (*Usage*)
``` php
$filename = "/tmp/epson.prn";
$connector = new Posprint\Connector\File($filename);
$printer = new Posprint\Printers\Epson($connector);
$printer->initialize();
$printer->setBold();
$printer->text("Hello World !!");
$printer->setBold();
$printer->lineFeed(2);
$printer->setAlign("C");
$printer->text("CENTRAL");
$printer->lineFeed(2);
$printer->cut();
$printer->send();
```

## Log de Alterações (*Change Log*)
Por favor veja o [Log de Alterações](CHANGELOG.md) para mais informações sobre as mudanças mais recentes.
*Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.*

## Testando (*Testing*)
``` bash
$ composer test
```
## Contribuindo (*Contributing*)
Por favor leia como contribuir em [CONTRIBUTING](CONTRIBUTING.md) e nosso [Código de Conduta](CONDUCT.md) para maiores detalhes.
*Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.*

## Security
Caso você encontre qualquer problema relacionado a segurança, informe diretamente o mantenedor linux.rlm@gmail.com ao invés de abrir uma ISSUE no github.
*If you discover any security related issues, please email :author_email instead of using the issue tracker.*

## Creditos (*Credits*)

- 
- 

## Licenças (*License*)
Este pacote está sendo disponibilizado sob as licenças LGPLv3 ou GPLv3 ou MIT, verifique o arquivo [Licenças](LICENSE.md) para maiores informações.
*The LGPLv3, GPLv3 and MIT License. Please see [License File](LICENSE.md) for more information.*
