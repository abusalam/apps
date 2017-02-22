<?php

define('FPDF_FONTPATH', 'font/');

require_once __DIR__ . '/fpdf_protection.php';

/**
 * Extended FPDF Class implemented Memory Image and Protection Support
 *
 * Memory Image Signature:
 * ===================
 * MemImage(string data [, float x [, float y [, float w [, float h [, mixed link]]]]])
 * MemImage($data, $x=null, $y=null, $w=0, $h=0, $link='')
 *
 * GDImage(resource im [, float x [, float y [, float w [, float h [, mixed link]]]]])
 * GDImage($im, $x=null, $y=null, $w=0, $h=0, $link='')
 *  #where im is the GD identifier.
 *
 * Ex.
 *   //Load an image into a variable
 *   $logo = file_get_contents('logo.jpg');
 *   //Output it
 *   $pdf->MemImage($logo, 50, 30);
 *
 * Protection Signature:
 * =====================
 *  SetProtection([array permissions [, string user_pass [, string owner_pass]]])
 *  SetProtection($permissions=array(), $user_pass='', $owner_pass=null)
 *
 *   permissions: the set of permissions. Empty by default (only viewing is allowed).
 *   user_pass: user password. Empty by default.
 *   owner_pass: owner password. If not specified, a random value is used.
 *
 *  Permissions:
 *    copy: copy text and images to the clipboard
 *    print: print the document
 *    modify: modify it (except for annotations and forms)
 *    annot-forms: add annotations and forms
 * Ex.
 *   SetProtection(array('print'));
 */
class PDF extends FPDF_Protection {

  public $Query;
  public $maxln;
  public $rh;
  public $colw;
  public $cols;
  public $fh = 3.5;
  private $PrintHeader = true;

  function PDF($orientation = 'P', $unit = 'mm', $format = 'A4') {
    $this->PDF_MemImage($orientation, $unit, $format);
    $this->SetAuthor('NIC Paschim Medinipur');
    $this->SetCreator('NIC Paschim Medinipur');
    $this->SetCompression(true);
    $this->SetProtection(array('print'));
    $this->AliasNbPages();
    $this->SetMargins(10, 10, 10);
    $this->SetAutoPageBreak(true, 5);
  }

  function AutoHeader($Print = true) {
    $this->PrintHeader = $Print;
  }

  function Header() {
    if ($this->PrintHeader) {
      $this->PreHeader();
      $i = 0;
      $this->SetFont('Arial', 'B', 8);
      $this->SetLineWidth(0.3);
      $this->SetColW($this->cols[1]);
      $row = $this->SplitLn($this->cols[0], 0);
      while ($i < count($row)) {
        $this->Wrap($this->colw[$i], $row[$i]);
        $i++;
      }
      $this->Ln();
    }
  }

  function Footer() {
    //Position at 1.5 cm from bottom
    $this->PreFooter();
    //Text color in gray
    $this->SetTextColor(128);
    $this->SetFont('Arial', '', 5);
    $this->Cell(0, 0, date("d/m/Y g:i:s A", time() + (15 * 60)), 0, 1, 'L');
    $this->Cell(0, 0, "Designed and Developed By National Informatics Centre, Paschim Medinipur", 0, 1, 'C');
    //Arial italic 8
    $this->SetFont('Arial', 'I', 6);
    //Page number
    $this->Cell(0, 0, 'Page: ' . $this->PageNo() . ' of {nb}', 0, 1, 'R');
  }

  function SplitLn($s, $SlNo = 1) {
    $c = 0;
    $this->maxln = 0;
    $ns = array();
    while ($c < count($s)) {
      $ns[$c] = $this->SplitStr($s[$c], $this->colw[$c + $SlNo]);
      $c++;
    }
    return $ns;
  }

  function SplitStr($s, $w) {
    $ns = "";
    $ln = "";
    $i = 0;
    $wd = preg_split('# #', $s);
    while ($i < count($wd)) {
      if ((($this->GetStringWidth($ln) + $this->GetStringWidth($wd[$i])) >= $w) && ($ln != "") && (substr_count($s, '|') == 0)) {
        $ns = $ns . trim($ln) . '|';
        $ln = "";
      } else {
        $ln = $ln . ' ' . $wd[$i];
        $i++;
      }
    }
    $ns = $ns . trim($ln);
    $this->maxln = max(substr_count($ns, '|'), $this->maxln);
    return $ns;
  }

  function SetColW($cols) {
    $i = 0;
    while ($i < count($cols)) {
      $this->colw[$i] = $cols[$i];
      $i++;
    }
  }

  function Wrap($w, $s, $b = 1, $align = 'C') {
    //$s=(substr_count($s,'|')>0)?$s:(($this->GetStringWidth($s)>$w)?$this->SplitLn($s,$w):$s);
    $nb = strlen($s);
    $p = $this->page;
    $h = ($this->maxln * $this->fh) + 6;
    if (($this->GetStringWidth($s) + 2) > $w) {
      $ox = $this->GetX();
      $oy = $this->GetY();

      $x = $this->GetX();
      $y = $this->GetY();
      $r = ((substr_count($s, '|') > 0 ? substr_count($s, '|') + 1 : 1));
      $oy = $oy + (($h - ($r * $this->fh)) / 2);

      do {
        $j = strpos($s, '|');
        $j = empty($j) ? strlen($s) : $j;
        $this->SetXY($ox, $oy);
        $this->SetDrawColor(255, 0, 0);
        if (($j > 0) || (strlen($s) > 0))
          $this->Cell($w, $this->fh, str_replace("|", ", ", substr($s, 0, $j)), 0, 0, $align);
        $oy+=$this->fh;
        $i = $j;
        if (strlen(substr($s, 0, $j)) > 0)
          $s = substr($s, $j + 1, $nb - $j);
        $nb = strlen($s);
      }while ($j);
      $this->SetXY($x, $y);
      $this->SetDrawColor(0);
      $this->SetTextColor(255, 255, 255);
      $this->Cell($w, $h, '', $b, 0, $align);
      $this->SetTextColor(0);
    }
    else
      $this->Cell($w, $h, str_replace("|", ", ", $s), $b, 0, $align);
  }

  function PreHeader() {
    $this->SetAutoPageBreak(true, 20);
  }

  function PreFooter() {
    $this->SetY(-4);
  }

}

?>