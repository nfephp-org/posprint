<?php

namespace Posprint\Common;

class Grf
{
    public static function bmp2grf($bmp)
    {
        $bmp = file_get_contents($bmp);
        $header = substr($bmp, 0, 2);
        if ($header != 'BM') {
            return false;
        }
        $posI = 16;
        $llow = (integer) substr($bmp, $posI + 1, 1);
        $numN = $llow * 256;
        $llow = (integer) substr($bmp, $posI, 1);
        $numN = ($numN + $llow) * 256;
                
        $llow = (integer) substr($bmp, $posI + 3, 1);
        $numN = ($numN + $llow) * 256;
        $llow = (integer) substr($bmp, $posI + 2, 1);
        $numN += $llow;
                
        $width = $numN;
        
        $posI = 21;
        $llow = (integer) substr($bmp, $posI + 1, 1);
        $numN = $llow * 256;
        $llow = (integer) substr($bmp, $posI, 1);
        $numN = ($numN + $llow) * 256;
        $llow = (integer) substr($bmp, $posI + 3, 1);
        $numN = ($numN + $llow) * 256;
        $llow = (integer) substr($bmp, $posI + 2, 1);
        $numN += $llow;
        
        $high = $numN;
        
        $llow = (integer) substr($bmp, 27, 1);
        $numN = $llow;
        $llow = (integer) substr($bmp, 29, 1);
        
        
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
    FileGet(1, LL, i + 1)
    N = LL * 256
    FileGet(1, LL, i)
    N = (N + LL) * 256

    FileGet(1, LL, i + 3)
    N = (N + LL) * 256
    FileGet(1, LL, i + 2)
    N += LL
    WID = N
    i = 21
    FileGet(1, LL, i + 1)
    N = LL * 256
    FileGet(1, LL, i)
    N = (N + LL) * 256
    FileGet(1, LL, i + 3)
    N = (N + LL) * 256
    FileGet(1, LL, i + 2)
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
 * 
 */