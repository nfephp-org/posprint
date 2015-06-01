<?php

namespace Posprint\Common;

/**
 * O mapa de caracteres ABICOMP são complementações do ASCII
 */

class ABICOMP
{
    public $char = array(
        //160 => ' ',
        161 => 'À',
        162 => 'Á',
        163 => 'Â',
        164 => 'Ã',
        165 => 'Ä',
        166 => 'Ç',
        167 => 'È',
        168 => 'É',
        169 => 'Ê',
        170 => 'Ë',
        171 => 'Ì',
        172 => 'Í',
        173 => 'Î',
        174 => 'Ï',
        175 => 'Ñ',
        176 => 'Ò',
        177 => 'Ó',
        178 => 'Ô',
        179 => 'Õ',
        180 => 'Ö',
        181 => 'Æ',
        182 => 'Ù',
        183 => 'Ú',
        184 => 'Û',
        185 => 'Ü',
        186 => 'Ÿ',
        187 => '“',
        188 => '£',
        189 => "'",
        190 => '§',
        //191 => 'o',
        192 => 'í',
        193 => 'à',
        194 => 'á',
        195 => 'â',
        196 => 'ã',
        197 => 'ä',
        198 => 'ç',
        199 => 'è',
        200 => 'é',
        201 => 'ê',
        202 => 'ë',
        203 => 'ì',
        204 => 'í',
        205 => 'î',
        206 => 'ï',
        207 => 'ñ',
        208 => 'ò',
        209 => 'ó',
        210 => 'ô',
        211 => 'õ',
        212 => 'ö',
        213 => 'æ',
        214 => 'ù',
        215 => 'ú',
        216 => 'û',
        217 => 'ü',
        218 => 'ÿ',
        219 => 'ß',
        //220 => 'a',
        //221 => 'o',
        222 => '¿',
        223 => '±'
    );
    
    /**
     * convert
     * Converte as letras em UTF8 para os codigos especiais da ABICOMP
     * @param string $text
     * @return string
     */
    public function convert($text = '')
    {
        $chars = array_flip($this->chars);
        $len = strlen($text);
        for ($xPos = 0; $xPos <= ($len-1); $xPos++) {
            $letra = substr($text, $xPos, 1);
            if (isset($chars[$letra])) {
                $ntext .= chr($chars[$letra]);
            } else {
                $ntext .= $letra;
            }
        }
        return $ntext;
    }
}
