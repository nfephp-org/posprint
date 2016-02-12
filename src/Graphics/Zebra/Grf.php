<?php

namespace Posprint\Graphics;

class Grf
{
    public static function convert($bitmap = null)
    {
        $withLB = true;
        $invertPixels = false;
        $outputFileName = 'logo';
        //Load the image into a string
        $file = fopen($bitmap, "rb");
        $read = fread($file, 10);
        //continue at the end of file
        while (! feof($file) && ($read <> "")) {
            $read .= fread($file, 1024);
        }
        fclose($file);
        $temp = unpack("H*", $read);
        $hex = $temp[1];
        $header = substr($hex, 0, 108);
        //Process the header
        //Structure: http://www.fastgraph.com/help/bmp_header_format.html
        if (substr($header, 0, 4) != "424d") {
            //is not a BMP file
            return false;
        }
        $origBytes = str_split($hex, 2);
        //Cut it in parts of 2 bytes
        $headerParts = str_split($header, 2);
        //total bytes
        $totalBytes = count($origBytes);
        //Get the width 4 bytes
        $width = hexdec($headerParts[19].$headerParts[18]);
        //Get the height 4 bytes
        $height = hexdec($headerParts[23].$headerParts[22]);
        
        $cor1  = hexdec($headerParts[26]);
        $cor2  = hexdec($headerParts[28]);
        
        $lenBytes = hexdec($headerParts[3].$headerParts[2])+hexdec($headerParts[5].$headerParts[4]);
        // Unset the header params
        unset($headerParts);
        
        echo "height: $height  width: $width total byte length: $totalBytes Bytes: $lenBytes <br>";
        
        $pixeloffset = hexdec($origBytes[10])+hexdec($origBytes[11]) + hexdec($origBytes[12])+hexdec($origBytes[13]);
        if ($pixeloffset == 62) {
            echo "pixel offset: $pixeloffset <BR>";
	} else {
            echo "pixel offset (WARNING! NOT THE DEFAULT OF 62): $pixeloffset <BR>";
	}
        
        $byteW = ceil($width/8);
        $newByteIndex = 0;
	for ($i=$lenBytes-1; $i>=$pixeloffset; $i--) {
            $tmp = $i-($byteW-1);
            $min = ($tmp+$byteW);
            for($j=$tmp; $j<$min; $j++) {
                $withoutHeaderBytes[] = pack("H",$origBytes[$j]);
                $newByteIndex++;
            }
            $i = $tmp;
	}
        echo "Bytes esperados : ".($lenBytes-$pixeloffset) ."  Bytes criados: $newByteIndex";
        $totWHB = count($withoutHeaderBytes);
        
        if ($invertPixels) {
            echo "pixels will be inverted!";
            for ($i=0; $i<$totWHB; $i++) {
                $withoutHeaderBytes[i] ^= 0xFF;
            }
        }
        var_dump($withoutHeaderBytes);
        $byteAsString = implode('',$withoutHeaderBytes);
        //$byteAsString = pack("H",$withoutHeaderBytes);

        if ($withLB) {
            $bytesAsCharArr = str_split($byteAsString, 2);
            $lineBreakCount = ceil($width/4);
	    echo "Adding line break every: $lineBreakCount bytes";
            $lineBreakedStr = "";
            $len = count($bytesAsCharArr);
            for ($i=0; $i<$len; $i++) {
                if ($i%$lineBreakCount == 0) {
                    $lineBreakedStr .= "\n";
		}
                $lineBreakedStr .= $bytesAsCharArr[$i];
            }
            $byteAsString = $lineBreakedStr;
        }        
        $wInBytes = ceil($width/8);
	$imageTemplate = "~DG" . $outputFileName . "," + $totWHB;
        $imageTemplate .= "," . $wInBytes . "," . $byteAsString;
        $handle = fopen($outputFileName.'.grf', "wb");
	fwrite($handle, $imageTemplate);
        fclose($handle);
	echo "Finished!  Check for file $outputFileName.grf in executing dir";

    }
}

/*
Public Function ConvertBmp2Grf(fileName As String, imageName As String) As Boolean
    Dim TI As String
    Dim i As Short
    Dim WID As Object
    Dim high As Object
    Dim TEM As Short, BMPL As Short, EFG As Short, n2 As String, LON As String
    Dim header_name As String, a As String, j As Short, COUN As Short, BASE1 As Short

    Dim L As String, TOT As String
    Dim N As Object
    Dim TOT1 As Integer
    Dim LL As Byte

    FileOpen(1, fileName, OpenMode.Binary, , , 1)  ' OPEN BMP FILE TO READ
    FileGet(1, LL, 1)
    TI = Convert.ToString(Chr(LL))
    FileGet(1, LL, 2)
    TI += Convert.ToString(Chr(LL))

    If TI <> "BM" Then
        FileClose()
        Return False
    End If

    i = 17
    FileGet(1, LL, i + 1) //18  = 0
    N = LL * 256  // = 0
    FileGet(1, LL, i) //17 = 0
    N = (N + LL) * 256 // = 0

    FileGet(1, LL, i + 3) //20 = 0
    N = (N + LL) * 256 // = 0
    FileGet(1, LL, i + 2) //19 = 0
    N += LL //=0
    WID = N // = 0 ???
    i = 21
    FileGet(1, LL, i + 1)
    N = LL * 256
    FileGet(1, LL, i)
    N = (N + LL) * 256
    FileGet(1, LL, i + 3) //24
    N = (N + LL) * 256
    FileGet(1, LL, i + 2) //23
    N += LL
    high = N
    FileGet(1, LL, 27)
    N = LL
    FileGet(1, LL, 29)

    If N <> 1 Or LL <> 1 Then
        'BMP has too many colors, only support monochrome images
        FileClose(1)
        Return False
    End If

    TEM = Int(WID / 8)
    If (WID Mod 8) <> 0 Then
        TEM += 1
    End If
    BMPL = TEM

    If (BMPL Mod 4) <> 0 Then
        BMPL += (4 - (BMPL Mod 4))
        EFG = 1
    End If

    n2 = fileName.Substring(0, fileName.LastIndexOf("\", StringComparison.Ordinal) + 1) + imageName + ".GRF"

    FileOpen(2, n2, OpenMode.Output) 'OPEN GRF TO OUTPUT
    TOT1 = TEM * high : TOT = Mid(Str(TOT1), 2)
    If Len(TOT) < 5 Then
        TOT = Strings.Left("00000", 5 - Len(TOT)) + TOT
    End If

    LON = Mid(Str(TEM), 2)

    If Len(LON) < 3 Then
        LON = Strings.Left("000", 3 - Len(LON)) + LON
    End If

    header_name = imageName
    PrintLine(2, "~DG" & header_name & "," & TOT & "," & LON & ",")

    For i = high To 1 Step -1
        a = ""
        For j = 1 To TEM
            COUN = 62 + (i - 1) * BMPL + j
            FileGet(1, LL, COUN)
            L = LL

            If j = TEM And (EFG = 1 Or (WID Mod 8) <> 0) Then
                BASE1 = 2 ^ ((TEM * 8 - WID) Mod 8)
                L = Int(L / BASE1) * BASE1 + BASE1 - 1
            End If
            L = Not L
            a += Right(Hex(L), 2)
        Next j
        PrintLine(2, a)
    Next i
    FileClose()

    Return True

End Function
 */