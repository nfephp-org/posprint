<?php

namespace Posprint\Common;

/**
 * Class ABICOMP
 * The map of ABICOMP characters are additions ASCII
 *
 * @category   NFePHP
 * @package    Posprint
 * @copyright  Copyright (c) 2015
 * @license    http://www.gnu.org/licenses/lesser.html LGPL v3
 * @author     Roberto L. Machado <linux.rlm at gmail dot com>
 * @link       http://github.com/nfephp-org/posprint for the canonical source repository
 */


class ABICOMP
{
    public static $abicompchar = array(
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
     * Converts the letters in UTF8 for the special codes of ABICOMP
     * @param string $text
     * @return string
     */
    public static function convert($text = '')
    {
        $chars = array_flip(self::$abicompchars);
        $len = strlen($text);
        $ntext = '';
        for ($xPos = 0; $xPos < $len; $xPos++) {
            $letra = substr($text, $xPos, 1);
            if (isset($chars[$letra])) {
                $letra = chr($chars[$letra]);
            }
            $ntext .= $letra;
        }
        return $ntext;
    }
}
