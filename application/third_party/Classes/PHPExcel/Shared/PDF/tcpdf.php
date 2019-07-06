<?php
//============================================================+
// File name   : tcpdf.php
// Version     : 5.9.009
// Begin       : 2002-08-03
// Last Update : 2010-10-21
// Author      : Nicola Asuni - Tecnick.com S.r.l - Via Della Pace, 11 - 09044 - Quartucciu (CA) - ITALY - www.tecnick.com - info@tecnick.com
// License     : GNU-LGPL v3 (http://www.gnu.org/copyleft/lesser.html)
// -------------------------------------------------------------------
// Copyright (C) 2002-2010  Nicola Asuni - Tecnick.com S.r.l.
//
// This file is part of TCPDF software library.
//
// TCPDF is free software: you can redistribute it and/or modify it
// under the terms of the GNU Lesser General Public License as
// published by the Free Software Foundation, either version 3 of the
// License, or (at your option) any later version.
//
// TCPDF is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
// See the GNU Lesser General Public License for more details.
//
// You should have received a copy of the GNU Lesser General Public License
// along with TCPDF.  If not, see <http://www.gnu.org/licenses/>.
//
// See LICENSE.TXT file for more information.
// -------------------------------------------------------------------
//
// Description : This is a PHP class for generating PDF documents without
//               requiring external extensions.
//
// NOTE:
//   This class was originally derived in 2002 from the Public
//   Domain FPDF class by Olivier Plathey (http://www.fpdf.org),
//   but now is almost entirely rewritten and contains thousands of
//   new lines of code and hundreds new features.
//
// Main features:
//  * no external libraries are required for the basic functions;
//  * all standard page formats, custom page formats, custom margins and units of measure;
//  * UTF-8 Unicode and Right-To-Left languages;
//  * TrueTypeUnicode, OpenTypeUnicode, TrueType, OpenType, Type1 and CID-0 fonts;
//  * font subsetting;
//  * methods to publish some XHTML + CSS code, Javascript and Forms;
//  * images, graphic (geometric figures) and transformation methods;
//  * supports JPEG, PNG and SVG images natively, all images supported by GD (GD, GD2, GD2PART, GIF, JPEG, PNG, BMP, XBM, XPM) and all images supported via ImagMagick (http://www.imagemagick.org/www/formats.html)
//  * 1D and 2D barcodes: CODE 39, ANSI MH10.8M-1983, USD-3, 3 of 9, CODE 93, USS-93, Standard 2 of 5, Interleaved 2 of 5, CODE 128 A/B/C, 2 and 5 Digits UPC-Based Extention, EAN 8, EAN 13, UPC-A, UPC-E, MSI, POSTNET, PLANET, RMS4CC (Royal Mail 4-state Customer Code), CBC (Customer Bar Code), KIX (Klant index - Customer index), Intelligent Mail Barcode, Onecode, USPS-B-3200, CODABAR, CODE 11, PHARMACODE, PHARMACODE TWO-TRACKS, QR-Code, PDF417;
//  * Grayscale, RGB, CMYK, Spot Colors and Transparencies;
//  * automatic page header and footer management;
//  * document encryption up to 256 bit and digital signature certifications;
//  * transactions to UNDO commands;
//  * PDF annotations, including links, text and file attachments;
//  * text rendering modes (fill, stroke and clipping);
//  * multiple columns mode;
//  * no-write page regions;
//  * bookmarks and table of content;
//  * text hyphenation;
//  * text stretching and spacing (tracking/kerning);
//  * automatic page break, line break and text alignments including justification;
//  * automatic page numbering and page groups;
//  * move and delete pages;
//  * page compression (requires php-zlib extension);
//  * XOBject Templates;
//
// -----------------------------------------------------------
// THANKS TO:
//
// Olivier Plathey (http://www.fpdf.org) for original FPDF.
// Efthimios Mavrogeorgiadis (emavro@yahoo.com) for suggestions on RTL language support.
// Klemen Vodopivec (http://www.fpdf.de/downloads/addons/37/) for Encryption algorithm.
// Warren Sherliker (wsherliker@gmail.com) for better image handling.
// dullus for text Justification.
// Bob Vincent (pillarsdotnet@users.sourceforge.net) for <li> value attribute.
// Patrick Benny for text stretch suggestion on Cell().
// Johannes Güntert for JavaScript support.
// Denis Van Nuffelen for Dynamic Form.
// Jacek Czekaj for multibyte justification
// Anthony Ferrara for the reintroduction of legacy image methods.
// Sourceforge user 1707880 (hucste) for line-trough mode.
// Larry Stanbery for page groups.
// Martin Hall-May for transparency.
// Aaron C. Spike for Polycurve method.
// Mohamad Ali Golkar, Saleh AlMatrafe, Charles Abbott for Arabic and Persian support.
// Moritz Wagner and Andreas Wurmser for graphic functions.
// Andrew Whitehead for core fonts support.
// Esteban Joël Marín for OpenType font conversion.
// Teus Hagen for several suggestions and fixes.
// Yukihiro Nakadaira for CID-0 CJK fonts fixes.
// Kosmas Papachristos for some CSS improvements.
// Marcel Partap for some fixes.
// Won Kyu Park for several suggestions, fixes and patches.
// Dominik Dzienia for QR-code support.
// Laurent Minguet for some suggestions.
// Christian Deligant for some suggestions and fixes.
// Anyone that has reported a bug or sent a suggestion.
//============================================================+

/**
 * This is a PHP class for generating PDF documents without requiring external extensions.<br>
 * TCPDF project (http://www.tcpdf.org) was originally derived in 2002 from the Public Domain FPDF class by Olivier Plathey (http://www.fpdf.org), but now is almost entirely rewritten.<br>
 * <h3>TCPDF main features are:</h3>
 * <ul>
 * <li>no external libraries are required for the basic functions;</li>
 * <li>all standard page formats, custom page formats, custom margins and units of measure;</li>
 * <li>UTF-8 Unicode and Right-To-Left languages;</li>
 * <li>TrueTypeUnicode, OpenTypeUnicode, TrueType, OpenType, Type1 and CID-0 fonts;</li>
 * <li>font subsetting;</li>
 * <li>methods to publish some XHTML + CSS code, Javascript and Forms;</li>
 * <li>images, graphic (geometric figures) and transformation methods;
 * <li>supports JPEG, PNG and SVG images natively, all images supported by GD (GD, GD2, GD2PART, GIF, JPEG, PNG, BMP, XBM, XPM) and all images supported via ImagMagick (http://www.imagemagick.org/www/formats.html)</li>
 * <li>1D and 2D barcodes: CODE 39, ANSI MH10.8M-1983, USD-3, 3 of 9, CODE 93, USS-93, Standard 2 of 5, Interleaved 2 of 5, CODE 128 A/B/C, 2 and 5 Digits UPC-Based Extention, EAN 8, EAN 13, UPC-A, UPC-E, MSI, POSTNET, PLANET, RMS4CC (Royal Mail 4-state Customer Code), CBC (Customer Bar Code), KIX (Klant index - Customer index), Intelligent Mail Barcode, Onecode, USPS-B-3200, CODABAR, CODE 11, PHARMACODE, PHARMACODE TWO-TRACKS, QR-Code, PDF417;</li>
 * <li>Grayscale, RGB, CMYK, Spot Colors and Transparencies;</li>
 * <li>automatic page header and footer management;</li>
 * <li>document encryption up to 256 bit and digital signature certifications;</li>
 * <li>transactions to UNDO commands;</li>
 * <li>PDF annotations, including links, text and file attachments;</li>
 * <li>text rendering modes (fill, stroke and clipping);</li>
 * <li>multiple columns mode;</li>
 * <li>no-write page regions;</li>
 * <li>bookmarks and table of content;</li>
 * <li>text hyphenation;</li>
 * <li>text stretching and spacing (tracking/kerning);</li>
 * <li>automatic page break, line break and text alignments including justification;</li>
 * <li>automatic page numbering and page groups;</li>
 * <li>move and delete pages;</li>
 * <li>page compression (requires php-zlib extension);</li>
 * <li>XOBject Templates;</li>
 * </ul>
 * Tools to encode your unicode fonts are on fonts/utils directory.</p>
 * @package com.tecnick.tcpdf
 * @abstract Class for generating PDF files on-the-fly without requiring external extensions.
 * @author Nicola Asuni
 * @copyright 2002-2010 Nicola Asuni - Tecnick.com S.r.l (www.tecnick.com) Via Della Pace, 11 - 09044 - Quartucciu (CA) - ITALY - www.tecnick.com - info@tecnick.com
 * @link http://www.tcpdf.org
 * @license http://www.gnu.org/copyleft/lesser.html LGPL
 * @version 5.9.009
 */

/**
 * main configuration file
 * (define the K_TCPDF_EXTERNAL_CONFIG constant to skip this file)
 */
require_once(dirname(__FILE__).'/config/tcpdf_config.php');

/**
 * define default PDF document producer
 */
define('PDF_PRODUCER', 'TCPDF 5.9.009 (http://www.tcpdf.org)');

/**
* This is a PHP class for generating PDF documents without requiring external extensions.<br>
* TCPDF project (http://www.tcpdf.org) has been originally derived in 2002 from the Public Domain FPDF class by Olivier Plathey (http://www.fpdf.org), but now is almost entirely rewritten.<br>
* @name TCPDF
* @package com.tecnick.tcpdf
* @version 5.9.009
* @author Nicola Asuni - info@tecnick.com
* @link http://www.tcpdf.org
* @license http://www.gnu.org/copyleft/lesser.html LGPL
*/
class TCPDF {

	// Protected properties

	/**
	 * @var current page number
	 * @access protected
	 */
	protected $page;

	/**
	 * @var current object number
	 * @access protected
	 */
	protected $n;

	/**
	 * @var array of object offsets
	 * @access protected
	 */
	protected $offsets;

	/**
	 * @var buffer holding in-memory PDF
	 * @access protected
	 */
	protected $buffer;

	/**
	 * @var array containing pages
	 * @access protected
	 */
	protected $pages = array();

	/**
	 * @var current document state
	 * @access protected
	 */
	protected $state;

	/**
	 * @var compression flag
	 * @access protected
	 */
	protected $compress;

	/**
	 * @var current page orientation (P = Portrait, L = Landscape)
	 * @access protected
	 */
	protected $CurOrientation;

	/**
	 * @var Page dimensions
	 * @access protected
	 */
	protected $pagedim = array();

	/**
	 * @var scale factor (number of points in user unit)
	 * @access protected
	 */
	protected $k;

	/**
	 * @var width of page format in points
	 * @access protected
	 */
	protected $fwPt;

	/**
	 * @var height of page format in points
	 * @access protected
	 */
	protected $fhPt;

	/**
	 * @var current width of page in points
	 * @access protected
	 */
	protected $wPt;

	/**
	 * @var current height of page in points
	 * @access protected
	 */
	protected $hPt;

	/**
	 * @var current width of page in user unit
	 * @access protected
	 */
	protected $w;

	/**
	 * @var current height of page in user unit
	 * @access protected
	 */
	protected $h;

	/**
	 * @var left margin
	 * @access protected
	 */
	protected $lMargin;

	/**
	 * @var top margin
	 * @access protected
	 */
	protected $tMargin;

	/**
	 * @var right margin
	 * @access protected
	 */
	protected $rMargin;

	/**
	 * @var page break margin
	 * @access protected
	 */
	protected $bMargin;

	/**
	 * @var array of cell internal paddings ('T' => top, 'R' => right, 'B' => bottom, 'L' => left)
	 * @since 5.9.000 (2010-10-03)
	 * @access protected
	 */
	protected $cell_padding = array('T' => 0, 'R' => 0, 'B' => 0, 'L' => 0);

	/**
	 * @var array of cell margins ('T' => top, 'R' => right, 'B' => bottom, 'L' => left)
	 * @since 5.9.000 (2010-10-04)
	 * @access protected
	 */
	protected $cell_margin = array('T' => 0, 'R' => 0, 'B' => 0, 'L' => 0);

	/**
	 * @var current horizontal position in user unit for cell positioning
	 * @access protected
	 */
	protected $x;

	/**
	 * @var current vertical position in user unit for cell positioning
	 * @access protected
	 */
	protected $y;

	/**
	 * @var height of last cell printed
	 * @access protected
	 */
	protected $lasth;

	/**
	 * @var line width in user unit
	 * @access protected
	 */
	protected $LineWidth;

	/**
	 * @var array of standard font names
	 * @access protected
	 */
	protected $CoreFonts;

	/**
	 * @var array of used fonts
	 * @access protected
	 */
	protected $fonts = array();

	/**
	 * @var array of font files
	 * @access protected
	 */
	protected $FontFiles = array();

	/**
	 * @var array of encoding differences
	 * @access protected
	 */
	protected $diffs = array();

	/**
	 * @var array of used images
	 * @access protected
	 */
	protected $images = array();

	/**
	 * @var array of Annotations in pages
	 * @access protected
	 */
	protected $PageAnnots = array();

	/**
	 * @var array of internal links
	 * @access protected
	 */
	protected $links = array();

	/**
	 * @var current font family
	 * @access protected
	 */
	protected $FontFamily;

	/**
	 * @var current font style
	 * @access protected
	 */
	protected $FontStyle;

	/**
	 * @var current font ascent (distance between font top and baseline)
	 * @access protected
	 * @since 2.8.000 (2007-03-29)
	 */
	protected $FontAscent;

	/**
	 * @var current font descent (distance between font bottom and baseline)
	 * @access protected
	 * @since 2.8.000 (2007-03-29)
	 */
	protected $FontDescent;

	/**
	 * @var underlining flag
	 * @access protected
	 */
	protected $underline;

	/**
	 * @var overlining flag
	 * @access protected
	 */
	protected $overline;

	/**
	 * @var current font info
	 * @access protected
	 */
	protected $CurrentFont;

	/**
	 * @var current font size in points
	 * @access protected
	 */
	protected $FontSizePt;

	/**
	 * @var current font size in user unit
	 * @access protected
	 */
	protected $FontSize;

	/**
	 * @var commands for drawing color
	 * @access protected
	 */
	protected $DrawColor;

	/**
	 * @var commands for filling color
	 * @access protected
	 */
	protected $FillColor;

	/**
	 * @var commands for text color
	 * @access protected
	 */
	protected $TextColor;

	/**
	 * @var indicates whether fill and text colors are different
	 * @access protected
	 */
	protected $ColorFlag;

	/**
	 * @var automatic page breaking
	 * @access protected
	 */
	protected $AutoPageBreak;

	/**
	 * @var threshold used to trigger page breaks
	 * @access protected
	 */
	protected $PageBreakTrigger;

	/**
	 * @var flag set when processing footer
	 * @access protected
	 */
	protected $InFooter = false;

	/**
	 * @var zoom display mode
	 * @access protected
	 */
	protected $ZoomMode;

	/**
	 * @var layout display mode
	 * @access protected
	 */
	protected $LayoutMode;

	/**
	 * @var title
	 * @access protected
	 */
	protected $title = '';

	/**
	 * @var subject
	 * @access protected
	 */
	protected $subject = '';

	/**
	 * @var author
	 * @access protected
	 */
	protected $author = '';

	/**
	 * @var keywords
	 * @access protected
	 */
	protected $keywords = '';

	/**
	 * @var creator
	 * @access protected
	 */
	protected $creator = '';

	/**
	 * @var alias for total number of pages
	 * @access protected
	 */
	protected $AliasNbPages = '{nb}';

	/**
	 * @var alias for page number
	 * @access protected
	 */
	protected $AliasNumPage = '{pnb}';

	/**
	 * @var right-bottom corner X coordinate of inserted image
	 * @since 2002-07-31
	 * @author Nicola Asuni
	 * @access protected
	 */
	protected $img_rb_x;

	/**
	 * @var right-bottom corner Y coordinate of inserted image
	 * @since 2002-07-31
	 * @author Nicola Asuni
	 * @access protected
	 */
	protected $img_rb_y;

	/**
	 * @var adjusting factor to convert pixels to user units.
	 * @since 2004-06-14
	 * @author Nicola Asuni
	 * @access protected
	 */
	protected $imgscale = 1;

	/**
	 * @var boolean set to true when the input text is unicode (require unicode fonts)
	 * @since 2005-01-02
	 * @author Nicola Asuni
	 * @access protected
	 */
	protected $isunicode = false;

	/**
	 * @var object containing unicode data
	 * @since 5.9.004 (2010-10-18)
	 * @author Nicola Asuni
	 * @access protected
	 */
	protected $unicode;

	/**
	 * @var PDF version
	 * @since 1.5.3
	 * @access protected
	 */
	protected $PDFVersion = '1.7';

	/**
	 * @var Minimum distance between header and top page margin.
	 * @access protected
	 */
	protected $header_margin;

	/**
	 * @var Minimum distance between footer and bottom page margin.
	 * @access protected
	 */
	protected $footer_margin;

	/**
	 * @var original left margin value
	 * @access protected
	 * @since 1.53.0.TC013
	 */
	protected $original_lMargin;

	/**
	 * @var original right margin value
	 * @access protected
	 * @since 1.53.0.TC013
	 */
	protected $original_rMargin;

	/**
	 * @var Header font.
	 * @access protected
	 */
	protected $header_font;

	/**
	 * @var Footer font.
	 * @access protected
	 */
	protected $footer_font;

	/**
	 * @var Language templates.
	 * @access protected
	 */
	protected $l;

	/**
	 * @var Barcode to print on page footer (only if set).
	 * @access protected
	 */
	protected $barcode = false;

	/**
	 * @var If true prints header
	 * @access protected
	 */
	protected $print_header = true;

	/**
	 * @var If true prints footer.
	 * @access protected
	 */
	protected $print_footer = true;

	/**
	 * @var Header image logo.
	 * @access protected
	 */
	protected $header_logo = '';

	/**
	 * @var Header image logo width in mm.
	 * @access protected
	 */
	protected $header_logo_width = 30;

	/**
	 * @var String to print as title on document header.
	 * @access protected
	 */
	protected $header_title = '';

	/**
	 * @var String to print on document header.
	 * @access protected
	 */
	protected $header_string = '';

	/**
	 * @var Default number of columns for html table.
	 * @access protected
	 */
	protected $default_table_columns = 4;

	// variables for html parser

	/**
	 * @var HTML PARSER: array to store current link and rendering styles.
	 * @access protected
	 */
	protected $HREF = array();

	/**
	 * @var store a list of available fonts on filesystem.
	 * @access protected
	 */
	protected $fontlist = array();

	/**
	 * @var current foreground color
	 * @access protected
	 */
	protected $fgcolor;

	/**
	 * @var HTML PARSER: array of boolean values, true in case of ordered list (OL), false otherwise.
	 * @access protected
	 */
	protected $listordered = array();

	/**
	 * @var HTML PARSER: array count list items on nested lists.
	 * @access protected
	 */
	protected $listcount = array();

	/**
	 * @var HTML PARSER: current list nesting level.
	 * @access protected
	 */
	protected $listnum = 0;

	/**
	 * @var HTML PARSER: indent amount for lists.
	 * @access protected
	 */
	protected $listindent = 0;

	/**
	 * @var HTML PARSER: current list indententation level.
	 * @access protected
	 */
	protected $listindentlevel = 0;

	/**
	 * @var current background color
	 * @access protected
	 */
	protected $bgcolor;

	/**
	 * @var Store temporary font size in points.
	 * @access protected
	 */
	protected $tempfontsize = 10;

	/**
	 * @var spacer for LI tags.
	 * @access protected
	 */
	protected $lispacer = '';

	/**
	 * @var default encoding
	 * @access protected
	 * @since 1.53.0.TC010
	 */
	protected $encoding = 'UTF-8';

	/**
	 * @var PHP internal encoding
	 * @access protected
	 * @since 1.53.0.TC016
	 */
	protected $internal_encoding;

	/**
	 * @var indicates if the document language is Right-To-Left
	 * @access protected
	 * @since 2.0.000
	 */
	protected $rtl = false;

	/**
	 * @var used to force RTL or LTR string inversion
	 * @access protected
	 * @since 2.0.000
	 */
	protected $tmprtl = false;

	// --- Variables used for document encryption:

	/**
	 * Indicates whether document is protected
	 * @access protected
	 * @since 2.0.000 (2008-01-02)
	 */
	protected $encrypted;

	/**
	 * Array containing encryption settings
	 * @access protected
	 * @since 5.0.005 (2010-05-11)
	 */
	protected $encryptdata = array();

	/**
	 * last RC4 key encrypted (cached for optimisation)
	 * @access protected
	 * @since 2.0.000 (2008-01-02)
	 */
	protected $last_enc_key;

	/**
	 * last RC4 computed key
	 * @access protected
	 * @since 2.0.000 (2008-01-02)
	 */
	protected $last_enc_key_c;

	/**
	 * Encryption padding
	 * @access protected
	 */
	protected $enc_padding = "\x28\xBF\x4E\x5E\x4E\x75\x8A\x41\x64\x00\x4E\x56\xFF\xFA\x01\x08\x2E\x2E\x00\xB6\xD0\x68\x3E\x80\x2F\x0C\xA9\xFE\x64\x53\x69\x7A";

	/**
	 * File ID (used on trailer)
	 * @access protected
	 * @since 5.0.005 (2010-05-12)
	 */
	protected $file_id;

	// --- bookmark ---

	/**
	 * Outlines for bookmark
	 * @access protected
	 * @since 2.1.002 (2008-02-12)
	 */
	protected $outlines = array();

	/**
	 * Outline root for bookmark
	 * @access protected
	 * @since 2.1.002 (2008-02-12)
	 */
	protected $OutlineRoot;

	// --- javascript and form ---

	/**
	 * javascript code
	 * @access protected
	 * @since 2.1.002 (2008-02-12)
	 */
	protected $javascript = '';

	/**
	 * javascript counter
	 * @access protected
	 * @since 2.1.002 (2008-02-12)
	 */
	protected $n_js;

	/**
	 * line trough state
	 * @access protected
	 * @since 2.8.000 (2008-03-19)
	 */
	protected $linethrough;

	/**
	 * Array with additional document-wide usage rights for the document.
	 * @access protected
	 * @since 5.8.014 (2010-08-23)
	 */
	protected $ur = array();

	/**
	 * Dot Per Inch Document Resolution (do not change)
	 * @access protected
	 * @since 3.0.000 (2008-03-27)
	 */
	protected $dpi = 72;

	/**
	 * Array of page numbers were a new page group was started
	 * @access protected
	 * @since 3.0.000 (2008-03-27)
	 */
	protected $newpagegroup = array();

	/**
	 * Contains the number of pages of the groups
	 * @access protected
	 * @since 3.0.000 (2008-03-27)
	 */
	protected $pagegroups;

	/**
	 * Contains the alias of the current page group
	 * @access protected
	 * @since 3.0.000 (2008-03-27)
	 */
	protected $currpagegroup;

	/**
	 * Restrict the rendering of some elements to screen or printout.
	 * @access protected
	 * @since 3.0.000 (2008-03-27)
	 */
	protected $visibility = 'all';

	/**
	 * Print visibility.
	 * @access protected
	 * @since 3.0.000 (2008-03-27)
	 */
	protected $n_ocg_print;

	/**
	 * View visibility.
	 * @access protected
	 * @since 3.0.000 (2008-03-27)
	 */
	protected $n_ocg_view;

	/**
	 * Array of transparency objects and parameters.
	 * @access protected
	 * @since 3.0.000 (2008-03-27)
	 */
	protected $extgstates;

	/**
	 * Set the default JPEG compression quality (1-100)
	 * @access protected
	 * @since 3.0.000 (2008-03-27)
	 */
	protected $jpeg_quality;

	/**
	 * Default cell height ratio.
	 * @access protected
	 * @since 3.0.014 (2008-05-23)
	 */
	protected $cell_height_ratio = K_CELL_HEIGHT_RATIO;

	/**
	 * PDF viewer preferences.
	 * @access protected
	 * @since 3.1.000 (2008-06-09)
	 */
	protected $viewer_preferences;

	/**
	 * A name object specifying how the document should be displayed when opened.
	 * @access protected
	 * @since 3.1.000 (2008-06-09)
	 */
	protected $PageMode;

	/**
	 * Array for storing gradient information.
	 * @access protected
	 * @since 3.1.000 (2008-06-09)
	 */
	protected $gradients = array();

	/**
	 * Array used to store positions inside the pages buffer.
	 * keys are the page numbers
	 * @access protected
	 * @since 3.2.000 (2008-06-26)
	 */
	protected $intmrk = array();

	/**
	 * Array used to store positions inside the pages buffer.
	 * keys are the page numbers
	 * @access protected
	 * @since 5.7.000 (2010-08-03)
	 */
	protected $bordermrk = array();

	/**
	 * Array used to store page positions to track empty pages.
	 * keys are the page numbers
	 * @access protected
	 * @since 5.8.007 (2010-08-18)
	 */
	protected $emptypagemrk = array();

	/**
	 * Array used to store content positions inside the pages buffer.
	 * keys are the page numbers
	 * @access protected
	 * @since 4.6.021 (2009-07-20)
	 */
	protected $cntmrk = array();

	/**
	 * Array used to store footer positions of each page.
	 * @access protected
	 * @since 3.2.000 (2008-07-01)
	 */
	protected $footerpos = array();

	/**
	 * Array used to store footer length of each page.
	 * @access protected
	 * @since 4.0.014 (2008-07-29)
	 */
	protected $footerlen = array();

	/**
	 * True if a newline is created.
	 * @access protected
	 * @since 3.2.000 (2008-07-01)
	 */
	protected $newline = true;

	/**
	 * End position of the latest inserted line
	 * @access protected
	 * @since 3.2.000 (2008-07-01)
	 */
	protected $endlinex = 0;

	/**
	 * PDF string for last line width
	 * @access protected
	 * @since 4.0.006 (2008-07-16)
	 */
	protected $linestyleWidth = '';

	/**
	 * PDF string for last line width
	 * @access protected
	 * @since 4.0.006 (2008-07-16)
	 */
	protected $linestyleCap = '0 J';

	/**
	 * PDF string for last line width
	 * @access protected
	 * @since 4.0.006 (2008-07-16)
	 */
	protected $linestyleJoin = '0 j';

	/**
	 * PDF string for last line width
	 * @access protected
	 * @since 4.0.006 (2008-07-16)
	 */
	protected $linestyleDash = '[] 0 d';

	/**
	 * True if marked-content sequence is open
	 * @access protected
	 * @since 4.0.013 (2008-07-28)
	 */
	protected $openMarkedContent = false;

	/**
	 * Count the latest inserted vertical spaces on HTML
	 * @access protected
	 * @since 4.0.021 (2008-08-24)
	 */
	protected $htmlvspace = 0;

	/**
	 * Array of Spot colors
	 * @access protected
	 * @since 4.0.024 (2008-09-12)
	 */
	protected $spot_colors = array();

	/**
	 * Symbol used for HTML unordered list items
	 * @access protected
	 * @since 4.0.028 (2008-09-26)
	 */
	protected $lisymbol = '';

	/**
	 * String used to mark the beginning and end of EPS image blocks
	 * @access protected
	 * @since 4.1.000 (2008-10-18)
	 */
	protected $epsmarker = 'x#!#EPS#!#x';

	/**
	 * Array of transformation matrix
	 * @access protected
	 * @since 4.2.000 (2008-10-29)
	 */
	protected $transfmatrix = array();

	/**
	 * Current key for transformation matrix
	 * @access protected
	 * @since 4.8.005 (2009-09-17)
	 */
	protected $transfmatrix_key = 0;

	/**
	 * Booklet mode for double-sided pages
	 * @access protected
	 * @since 4.2.000 (2008-10-29)
	 */
	protected $booklet = false;

	/**
	 * Epsilon value used for float calculations
	 * @access protected
	 * @since 4.2.000 (2008-10-29)
	 */
	protected $feps = 0.005;

	/**
	 * Array used for custom vertical spaces for HTML tags
	 * @access protected
	 * @since 4.2.001 (2008-10-30)
	 */
	protected $tagvspaces = array();

	/**
	 * @var HTML PARSER: custom indent amount for lists.
	 * Negative value means disabled.
	 * @access protected
	 * @since 4.2.007 (2008-11-12)
	 */
	protected $customlistindent = -1;

	/**
	 * @var if true keeps the border open for the cell sides that cross the page.
	 * @access protected
	 * @since 4.2.010 (2008-11-14)
	 */
	protected $opencell = true;

	/**
	 * @var array of files to embedd
	 * @access protected
	 * @since 4.4.000 (2008-12-07)
	 */
	protected $embeddedfiles = array();

	/**
	 * @var boolean true when inside html pre tag
	 * @access protected
	 * @since 4.4.001 (2008-12-08)
	 */
	protected $premode = false;

	/**
	 * Array used to store positions of graphics transformation blocks inside the page buffer.
	 * keys are the page numbers
	 * @access protected
	 * @since 4.4.002 (2008-12-09)
	 */
	protected $transfmrk = array();

	/**
	 * Default color for html links
	 * @access protected
	 * @since 4.4.003 (2008-12-09)
	 */
	protected $htmlLinkColorArray = array(0, 0, 255);

	/**
	 * Default font style to add to html links
	 * @access protected
	 * @since 4.4.003 (2008-12-09)
	 */
	protected $htmlLinkFontStyle = 'U';

	/**
	 * Counts the number of pages.
	 * @access protected
	 * @since 4.5.000 (2008-12-31)
	 */
	protected $numpages = 0;

	/**
	 * Array containing page lengths in bytes.
	 * @access protected
	 * @since 4.5.000 (2008-12-31)
	 */
	protected $pagelen = array();

	/**
	 * Counts the number of pages.
	 * @access protected
	 * @since 4.5.000 (2008-12-31)
	 */
	protected $numimages = 0;

	/**
	 * Store the image keys.
	 * @access protected
	 * @since 4.5.000 (2008-12-31)
	 */
	protected $imagekeys = array();

	/**
	 * Length of the buffer in bytes.
	 * @access protected
	 * @since 4.5.000 (2008-12-31)
	 */
	protected $bufferlen = 0;

	/**
	 * If true enables disk caching.
	 * @access protected
	 * @since 4.5.000 (2008-12-31)
	 */
	protected $diskcache = false;

	/**
	 * Counts the number of fonts.
	 * @access protected
	 * @since 4.5.000 (2009-01-02)
	 */
	protected $numfonts = 0;

	/**
	 * Store the font keys.
	 * @access protected
	 * @since 4.5.000 (2009-01-02)
	 */
	protected $fontkeys = array();

	/**
	 * Store the font object IDs.
	 * @access protected
	 * @since 4.8.001 (2009-09-09)
	 */
	protected $font_obj_ids = array();

	/**
	 * Store the fage status (true when opened, false when closed).
	 * @access protected
	 * @since 4.5.000 (2009-01-02)
	 */
	protected $pageopen = array();

	/**
	 * Default monospaced font
	 * @access protected
	 * @since 4.5.025 (2009-03-10)
	 */
	protected $default_monospaced_font = 'courier';

	/**
	 * Used to store a cloned copy of the current class object
	 * @access protected
	 * @since 4.5.029 (2009-03-19)
	 */
	protected $objcopy;

	/**
	 * Array used to store the lengths of cache files
	 * @access protected
	 * @since 4.5.029 (2009-03-19)
	 */
	protected $cache_file_length = array();

	/**
	 * Table header content to be repeated on each new page
	 * @access protected
	 * @since 4.5.030 (2009-03-20)
	 */
	protected $thead = '';

	/**
	 * Margins used for table header.
	 * @access protected
	 * @since 4.5.030 (2009-03-20)
	 */
	protected $theadMargins = array();

	/**
	 * Cache array for UTF8StringToArray() method.
	 * @access protected
	 * @since 4.5.037 (2009-04-07)
	 */
	protected $cache_UTF8StringToArray = array();

	/**
	 * Maximum size of cache array used for UTF8StringToArray() method.
	 * @access protected
	 * @since 4.5.037 (2009-04-07)
	 */
	protected $cache_maxsize_UTF8StringToArray = 8;

	/**
	 * Current size of cache array used for UTF8StringToArray() method.
	 * @access protected
	 * @since 4.5.037 (2009-04-07)
	 */
	protected $cache_size_UTF8StringToArray = 0;

	/**
	 * If true enables document signing
	 * @access protected
	 * @since 4.6.005 (2009-04-24)
	 */
	protected $sign = false;

	/**
	 * Signature data
	 * @access protected
	 * @since 4.6.005 (2009-04-24)
	 */
	protected $signature_data = array();

	/**
	 * Signature max length
	 * @access protected
	 * @since 4.6.005 (2009-04-24)
	 */
	protected $signature_max_length = 11742;

	/**
	 * data for signature appearance
	 * @access protected
	 * @since 5.3.011 (2010-06-16)
	 */
	protected $signature_appearance = array('page' => 1, 'rect' => '0 0 0 0');

	/**
	 * Regular expression used to find blank characters used for word-wrapping.
	 * @access protected
	 * @since 4.6.006 (2009-04-28)
	 */
	protected $re_spaces = '/[^\S\xa0]/';

	/**
	 * Array of parts $re_spaces
	 * @access protected
	 * @since 5.5.011 (2010-07-09)
	 */
	protected $re_space = array('p' => '[^\S\xa0]', 'm' => '');

	/**
	 * Signature object ID
	 * @access protected
	 * @since 4.6.022 (2009-06-23)
	 */
	protected $sig_obj_id = 0;

	/**
	 * ByteRange placemark used during signature process.
	 * @access protected
	 * @since 4.6.028 (2009-08-25)
	 */
	protected $byterange_string = '/ByteRange[0 ********** ********** **********]';

	/**
	 * Placemark used during signature process.
	 * @access protected
	 * @since 4.6.028 (2009-08-25)
	 */
	protected $sig_annot_ref = '***SIGANNREF*** 0 R';

	/**
	 * ID of page objects
	 * @access protected
	 * @since 4.7.000 (2009-08-29)
	 */
	protected $page_obj_id = array();

	/**
	 * List of form annotations IDs
	 * @access protected
	 * @since 4.8.000 (2009-09-07)
	 */
	protected $form_obj_id = array();

	/**
	 * Deafult Javascript field properties. Possible values are described on official Javascript for Acrobat API reference. Annotation options can be directly specified using the 'aopt' entry.
	 * @access protected
	 * @since 4.8.000 (2009-09-07)
	 */
	protected $default_form_prop = array('lineWidth'=>1, 'borderStyle'=>'solid', 'fillColor'=>array(255, 255, 255), 'strokeColor'=>array(128, 128, 128));

	/**
	 * Javascript objects array
	 * @access protected
	 * @since 4.8.000 (2009-09-07)
	 */
	protected $js_objects = array();

	/**
	 * Current form action (used during XHTML rendering)
	 * @access protected
	 * @since 4.8.000 (2009-09-07)
	 */
	protected $form_action = '';

	/**
	 * Current form encryption type (used during XHTML rendering)
	 * @access protected
	 * @since 4.8.000 (2009-09-07)
	 */
	protected $form_enctype = 'application/x-www-form-urlencoded';

	/**
	 * Current method to submit forms.
	 * @access protected
	 * @since 4.8.000 (2009-09-07)
	 */
	protected $form_mode = 'post';

	/**
	 * List of fonts used on form fields (fontname => fontkey).
	 * @access protected
	 * @since 4.8.001 (2009-09-09)
	 */
	protected $annotation_fonts = array();

	/**
	 * List of radio buttons parent objects.
	 * @access protected
	 * @since 4.8.001 (2009-09-09)
	 */
	protected $radiobutton_groups = array();

	/**
	 * List of radio group objects IDs
	 * @access protected
	 * @since 4.8.001 (2009-09-09)
	 */
	protected $radio_groups = array();

	/**
	 * Text indentation value (used for text-indent CSS attribute)
	 * @access protected
	 * @since 4.8.006 (2009-09-23)
	 */
	protected $textindent = 0;

	/**
	 * Store page number when startTransaction() is called.
	 * @access protected
	 * @since 4.8.006 (2009-09-23)
	 */
	protected $start_transaction_page = 0;

	/**
	 * Store Y position when startTransaction() is called.
	 * @access protected
	 * @since 4.9.001 (2010-03-28)
	 */
	protected $start_transaction_y = 0;

	/**
	 * True when we are printing the thead section on a new page
	 * @access protected
	 * @since 4.8.027 (2010-01-25)
	 */
	protected $inthead = false;

	/**
	 * Array of column measures (width, space, starting Y position)
	 * @access protected
	 * @since 4.9.001 (2010-03-28)
	 */
	protected $columns = array();

	/**
	 * Number of colums
	 * @access protected
	 * @since 4.9.001 (2010-03-28)
	 */
	protected $num_columns = 1;

	/**
	 * Current column number
	 * @access protected
	 * @since 4.9.001 (2010-03-28)
	 */
	protected $current_column = 0;

	/**
	 * Starting page for columns
	 * @access protected
	 * @since 4.9.001 (2010-03-28)
	 */
	protected $column_start_page = 0;

	/**
	 * Maximum page and column selected
	 * @access protected
	 * @since 5.8.000 (2010-08-11)
	 */
	protected $maxselcol = array('page' => 0, 'column' => 0);

	/**
	 * Array of: X difference between table cell x start and starting page margin, cellspacing, cellpadding
	 * @access protected
	 * @since 5.8.000 (2010-08-11)
	 */
	protected $colxshift = array('x' => 0, 's' => 0, 'p' => 0);

	/**
	 * Text rendering mode: 0 = Fill text; 1 = Stroke text; 2 = Fill, then stroke text; 3 = Neither fill nor stroke text (invisible); 4 = Fill text and add to path for clipping; 5 = Stroke text and add to path for clipping; 6 = Fill, then stroke text and add to path for clipping; 7 = Add text to path for clipping.
	 * @access protected
	 * @since 4.9.008 (2010-04-03)
	 */
	protected $textrendermode = 0;

	/**
	 * Text stroke width in doc units
	 * @access protected
	 * @since 4.9.008 (2010-04-03)
	 */
	protected $textstrokewidth = 0;

	/**
	 * @var current stroke color
	 * @access protected
	 * @since 4.9.008 (2010-04-03)
	 */
	protected $strokecolor;

	/**
	 * @var default unit of measure for document
	 * @access protected
	 * @since 5.0.000 (2010-04-22)
	 */
	protected $pdfunit = 'mm';

	/**
	 * @var true when we are on TOC (Table Of Content) page
	 * @access protected
	 */
	protected $tocpage = false;

	/**
	 * @var If true convert vector images (SVG, EPS) to raster image using GD or ImageMagick library.
	 * @access protected
	 * @since 5.0.000 (2010-04-26)
	 */
	protected $rasterize_vector_images = false;

	/**
	 * @var If true enables font subsetting by default
	 * @access protected
	 * @since 5.3.002 (2010-06-07)
	 */
	protected $font_subsetting = true;

	/**
	 * @var Array of default graphic settings
	 * @access protected
	 * @since 5.5.008 (2010-07-02)
	 */
	protected $default_graphic_vars = array();

	/**
	 * @var Array of XObjects
	 * @access protected
	 * @since 5.8.014 (2010-08-23)
	 */
	protected $xobjects = array();

	/**
	 * @var boolean true when we are inside an XObject
	 * @access protected
	 * @since 5.8.017 (2010-08-24)
	 */
	protected $inxobj = false;

	/**
	 * @var current XObject ID
	 * @access protected
	 * @since 5.8.017 (2010-08-24)
	 */
	protected $xobjid = '';

	/**
	 * @var percentage of character stretching
	 * @access protected
	 * @since 5.9.000 (2010-09-29)
	 */
	protected $font_stretching = 100;

	/**
	 * @var increases or decreases the space between characters in a text by the specified amount (tracking/kerning).
	 * @access protected
	 * @since 5.9.000 (2010-09-29)
	 */
	protected $font_spacing = 0;

	/**
	 * @var array of no-write regions
	 * ('page' => page number or empy for current page, 'xt' => X top, 'yt' => Y top, 'xb' => X bottom, 'yb' => Y bottom, 'side' => page side 'L' = left or 'R' = right)
	 * @access protected
	 * @since 5.9.003 (2010-10-14)
	 */
	protected $page_regions = array();

	/**
	 * @var array containing HTML color names and values
	 * @access protected
	 * @since 5.9.004 (2010-10-18)
	 */
	protected $webcolor = array();

	/**
	 * @var directory used for the last SVG image
	 * @access protected
	 * @since 5.0.000 (2010-05-05)
	 */
	protected $svgdir = '';

	/**
	 * @var Deafult unit of measure for SVG
	 * @access protected
	 * @since 5.0.000 (2010-05-02)
	 */
	protected $svgunit = 'px';

	/**
	 * @var array of SVG gradients
	 * @access protected
	 * @since 5.0.000 (2010-05-02)
	 */
	protected $svggradients = array();

	/**
	 * @var ID of last SVG gradient
	 * @access protected
	 * @since 5.0.000 (2010-05-02)
	 */
	protected $svggradientid = 0;

	/**
	 * @var true when in SVG defs group
	 * @access protected
	 * @since 5.0.000 (2010-05-02)
	 */
	protected $svgdefsmode = false;

	/**
	 * @var array of SVG defs
	 * @access protected
	 * @since 5.0.000 (2010-05-02)
	 */
	protected $svgdefs = array();

	/**
	 * @var true when in SVG clipPath tag
	 * @access protected
	 * @since 5.0.000 (2010-04-26)
	 */
	protected $svgclipmode = false;

	/**
	 * @var array of SVG clipPath commands
	 * @access protected
	 * @since 5.0.000 (2010-05-02)
	 */
	protected $svgclippaths = array();

	/**
	 * @var array of SVG clipPath tranformation matrix
	 * @access protected
	 * @since 5.8.022 (2010-08-31)
	 */
	protected $svgcliptm = array();

	/**
	 * @var ID of last SVG clipPath
	 * @access protected
	 * @since 5.0.000 (2010-05-02)
	 */
	protected $svgclipid = 0;

	/**
	 * @var svg text
	 * @access protected
	 * @since 5.0.000 (2010-05-02)
	 */
	protected $svgtext = '';

	/**
	 * @var svg text properties
	 * @access protected
	 * @since 5.8.013 (2010-08-23)
	 */
	protected $svgtextmode = array();

	/**
	 * @var array of hinheritable SVG properties
	 * @access protected
	 * @since 5.0.000 (2010-05-02)
	 */
	protected $svginheritprop = array('clip-rule', 'color', 'color-interpolation', 'color-interpolation-filters', 'color-profile', 'color-rendering', 'cursor', 'direction', 'fill', 'fill-opacity', 'fill-rule', 'font', 'font-family', 'font-size', 'font-size-adjust', 'font-stretch', 'font-style', 'font-variant', 'font-weight', 'glyph-orientation-horizontal', 'glyph-orientation-vertical', 'image-rendering', 'kerning', 'letter-spacing', 'marker', 'marker-end', 'marker-mid', 'marker-start', 'pointer-events', 'shape-rendering', 'stroke', 'stroke-dasharray', 'stroke-dashoffset', 'stroke-linecap', 'stroke-linejoin', 'stroke-miterlimit', 'stroke-opacity', 'stroke-width', 'text-anchor', 'text-rendering', 'visibility', 'word-spacing', 'writing-mode');

	/**
	 * @var array of SVG properties
	 * @access protected
	 * @since 5.0.000 (2010-05-02)
	 */
	protected $svgstyles = array(array(
		'alignment-baseline' => 'auto',
		'baseline-shift' => 'baseline',
		'clip' => 'auto',
		'clip-path' => 'none',
		'clip-rule' => 'nonzero',
		'color' => 'black',
		'color-interpolation' => 'sRGB',
		'color-interpolation-filters' => 'linearRGB',
		'color-profile' => 'auto',
		'color-rendering' => 'auto',
		'cursor' => 'auto',
		'direction' => 'ltr',
		'display' => 'inline',
		'dominant-baseline' => 'auto',
		'enable-background' => 'accumulate',
		'fill' => 'black',
		'fill-opacity' => 1,
		'fill-rule' => 'nonzero',
		'filter' => 'none',
		'flood-color' => 'black',
		'flood-opacity' => 1,
		'font' => '',
		'font-family' => 'helvetica',
		'font-size' => 'medium',
		'font-size-adjust' => 'none',
		'font-stretch' => 'normal',
		'font-style' => 'normal',
		'font-variant' => 'normal',
		'font-weight' => 'normal',
		'glyph-orientation-horizontal' => '0deg',
		'glyph-orientation-vertical' => 'auto',
		'image-rendering' => 'auto',
		'kerning' => 'auto',
		'letter-spacing' => 'normal',
		'lighting-color' => 'white',
		'marker' => '',
		'marker-end' => 'none',
		'marker-mid' => 'none',
		'marker-start' => 'none',
		'mask' => 'none',
		'opacity' => 1,
		'overflow' => 'auto',
		'pointer-events' => 'visiblePainted',
		'shape-rendering' => 'auto',
		'stop-color' => 'black',
		'stop-opacity' => 1,
		'stroke' => 'none',
		'stroke-dasharray' => 'none',
		'stroke-dashoffset' => 0,
		'stroke-linecap' => 'butt',
		'stroke-linejoin' => 'miter',
		'stroke-miterlimit' => 4,
		'stroke-opacity' => 1,
		'stroke-width' => 1,
		'text-anchor' => 'start',
		'text-decoration' => 'none',
		'text-rendering' => 'auto',
		'unicode-bidi' => 'normal',
		'visibility' => 'visible',
		'word-spacing' => 'normal',
		'writing-mode' => 'lr-tb',
		'text-color' => 'black',
		'transfmatrix' => array(1, 0, 0, 1, 0, 0)
		));

	//------------------------------------------------------------
	// METHODS
	//------------------------------------------------------------

	/**
	 * This is the class constructor.
	 * It allows to set up the page format, the orientation and the measure unit used in all the methods (except for the font sizes).
	 * @param string $orientation page orientation. Possible values are (case insensitive):<ul><li>P or Portrait (default)</li><li>L or Landscape</li><li>'' (empty string) for automatic orientation</li></ul>
	 * @param string $unit User measure unit. Possible values are:<ul><li>pt: point</li><li>mm: millimeter (default)</li><li>cm: centimeter</li><li>in: inch</li></ul><br />A point equals 1/72 of inch, that is to say about 0.35 mm (an inch being 2.54 cm). This is a very common unit in typography; font sizes are expressed in that unit.
	 * @param mixed $format The format used for pages. It can be either: one of the string values specified at getPageSizeFromFormat() or an array of parameters specified at setPageFormat().
	 * @param boolean $unicode TRUE means that the input text is unicode (default = true)
	 * @param boolean $diskcache if TRUE reduce the RAM memory usage by caching temporary data on filesystem (slower).
	 * @param String $encoding charset encoding; default is UTF-8
	 * @access public
	 * @see getPageSizeFromFormat(), setPageFormat()
	 */
	public function __construct($orientation='P', $unit='mm', $format='A4', $unicode=true, $encoding='UTF-8', $diskcache=false) {
		/* Set internal character encoding to ASCII */
		if (function_exists('mb_internal_encoding') AND mb_internal_encoding()) {
			$this->internal_encoding = mb_internal_encoding();
			mb_internal_encoding('ASCII');
		}
		require(dirname(__FILE__).'/htmlcolors.php');
		$this->webcolor = $webcolor;
		require_once(dirname(__FILE__).'/unicode_data.php');
		$this->unicode = new TCPDF_UNICODE_DATA();
		$this->font_obj_ids = array();
		$this->page_obj_id = array();
		$this->form_obj_id = array();
		// set disk caching
		$this->diskcache = $diskcache ? true : false;
		// set language direction
		$this->rtl = false;
		$this->tmprtl = false;
		// some checks
		$this->_dochecks();
		// initialization of properties
		$this->isunicode = $unicode;
		$this->page = 0;
		$this->transfmrk[0] = array();
		$this->pagedim = array();
		$this->n = 2;
		$this->buffer = '';
		$this->pages = array();
		$this->state = 0;
		$this->fonts = array();
		$this->FontFiles = array();
		$this->diffs = array();
		$this->images = array();
		$this->links = array();
		$this->gradients = array();
		$this->InFooter = false;
		$this->lasth = 0;
		$this->FontFamily = 'helvetica';
		$this->FontStyle = '';
		$this->FontSizePt = 12;
		$this->underline = false;
		$this->overline = false;
		$this->linethrough = false;
		$this->DrawColor = '0 G';
		$this->FillColor = '0 g';
		$this->TextColor = '0 g';
		$this->ColorFlag = false;
		// encryption values
		$this->encrypted = false;
		$this->last_enc_key = '';
		// standard Unicode fonts
		$this->CoreFonts = array(
			'courier'=>'Courier',
			'courierB'=>'Courier-Bold',
			'courierI'=>'Courier-Oblique',
			'courierBI'=>'Courier-BoldOblique',
			'helvetica'=>'Helvetica',
			'helveticaB'=>'Helvetica-Bold',
			'helveticaI'=>'Helvetica-Oblique',
			'helveticaBI'=>'Helvetica-BoldOblique',
			'times'=>'Times-Roman',
			'timesB'=>'Times-Bold',
			'timesI'=>'Times-Italic',
			'timesBI'=>'Times-BoldItalic',
			'symbol'=>'Symbol',
			'zapfdingbats'=>'ZapfDingbats'
		);
		// set scale factor
		$this->setPageUnit($unit);
		// set page format and orientation
		$this->setPageFormat($format, $orientation);
		// page margins (1 cm)
		$margin = 28.35 / $this->k;
		$this->SetMargins($margin, $margin);
		// internal cell padding
		$cpadding = $margin / 10;
		$this->setCellPaddings($cpadding, 0, $cpadding, 0);
		// cell margins
		$this->setCellMargins(0, 0, 0, 0);
		// line width (0.2 mm)
		$this->LineWidth = 0.57 / $this->k;
		$this->linestyleWidth = sprintf('%.2F w', ($this->LineWidth * $this->k));
		$this->linestyleCap = '0 J';
		$this->linestyleJoin = '0 j';
		$this->linestyleDash = '[] 0 d';
		// automatic page break
		$this->SetAutoPageBreak(true, (2 * $margin));
		// full width display mode
		$this->SetDisplayMode('fullwidth');
		// compression
		$this->SetCompression(true);
		// set default PDF version number
		$this->PDFVersion = '1.7';
		$this->encoding = $encoding;
		$this->HREF = array();
		$this->getFontsList();
		$this->fgcolor = array('R' => 0, 'G' => 0, 'B' => 0);
		$this->strokecolor = array('R' => 0, 'G' => 0, 'B' => 0);
		$this->bgcolor = array('R' => 255, 'G' => 255, 'B' => 255);
		$this->extgstates = array();
		// user's rights
		$this->sign = false;
		$this->ur['enabled'] = false;
		$this->ur['document'] = '/FullSave';
		$this->ur['annots'] = '/Create/Delete/Modify/Copy/Import/Export';
		$this->ur['form'] = '/Add/Delete/FillIn/Import/Export/SubmitStandalone/SpawnTemplate';
		$this->ur['signature'] = '/Modify';
		$this->ur['ef'] = '/Create/Delete/Modify/Import';
		$this->ur['formex'] = '';
		$this->signature_appearance = array('page' => 1, 'rect' => '0 0 0 0');
		// set default JPEG quality
		$this->jpeg_quality = 75;
		// initialize some settings
		$this->utf8Bidi(array(''), '');
		// set default font
		$this->SetFont($this->FontFamily, $this->FontStyle, $this->FontSizePt);
		// check if PCRE Unicode support is enabled
		if ($this->isunicode AND (@preg_match('/\pL/u', 'a') == 1)) {
			// PCRE unicode support is turned ON
			// \p{Z} or \p{Separator}: any kind of Unicode whitespace or invisible separator.
			// \p{Lo} or \p{Other_Letter}: a Unicode letter or ideograph that does not have lowercase and uppercase variants.
			// \p{Lo} is needed because Chinese characters are packed next to each other without spaces in between.
			//$this->setSpacesRE('/[^\S\P{Z}\P{Lo}\xa0]/u');
			$this->setSpacesRE('/[^\S\P{Z}\xa0]/u');
		} else {
			// PCRE unicode support is turned OFF
			$this->setSpacesRE('/[^\S\xa0]/');
		}
		$this->default_form_prop = array('lineWidth'=>1, 'borderStyle'=>'solid', 'fillColor'=>array(255, 255, 255), 'strokeColor'=>array(128, 128, 128));
		// set file ID for trailer
		$this->file_id = md5($this->getRandomSeed('TCPDF'.$orientation.$unit.$format.$encoding));
		// get default graphic vars
		$this->default_graphic_vars = $this->getGraphicVars();
	}

	/**
	 * Default destructor.
	 * @access public
	 * @since 1.53.0.TC016
	 */
	public function __destruct() {
		// restore internal encoding
		if (isset($this->internal_encoding) AND !empty($this->internal_encoding)) {
			mb_internal_encoding($this->internal_encoding);
		}
		// unset all class variables
		$this->_destroy(true);
	}

	/**
	 * Set the units of measure for the document.
	 * @param string $unit User measure unit. Possible values are:<ul><li>pt: point</li><li>mm: millimeter (default)</li><li>cm: centimeter</li><li>in: inch</li></ul><br />A point equals 1/72 of inch, that is to say about 0.35 mm (an inch being 2.54 cm). This is a very common unit in typography; font sizes are expressed in that unit.
	 * @access public
	 * @since 3.0.015 (2008-06-06)
	 */
	public function setPageUnit($unit) {
		$unit = strtolower($unit);
		//Set scale factor
		switch ($unit) {
			// points
			case 'px':
			case 'pt': {
				$this->k = 1;
				break;
			}
			// millimeters
			case 'mm': {
				$this->k = $this->dpi / 25.4;
				break;
			}
			// centimeters
			case 'cm': {
				$this->k = $this->dpi / 2.54;
				break;
			}
			// inches
			case 'in': {
				$this->k = $this->dpi;
				break;
			}
			// unsupported unit
			default : {
				$this->Error('Incorrect unit: '.$unit);
				break;
			}
		}
		$this->pdfunit = $unit;
		if (isset($this->CurOrientation)) {
			$this->setPageOrientation($this->CurOrientation);
		}
	}

	/**
	 * Get page dimensions from format name.
	 * @param mixed $format The format name. It can be: <ul>
	 * <li><b>ISO 216 A Series + 2 SIS 014711 extensions</b></li>
	 * <li>A0 (841x1189 mm ; 33.11x46.81 in)</li>
	 * <li>A1 (594x841 mm ; 23.39x33.11 in)</li>
	 * <li>A2 (420x594 mm ; 16.54x23.39 in)</li>
	 * <li>A3 (297x420 mm ; 11.69x16.54 in)</li>
	 * <li>A4 (210x297 mm ; 8.27x11.69 in)</li>
	 * <li>A5 (148x210 mm ; 5.83x8.27 in)</li>
	 * <li>A6 (105x148 mm ; 4.13x5.83 in)</li>
	 * <li>A7 (74x105 mm ; 2.91x4.13 in)</li>
	 * <li>A8 (52x74 mm ; 2.05x2.91 in)</li>
	 * <li>A9 (37x52 mm ; 1.46x2.05 in)</li>
	 * <li>A10 (26x37 mm ; 1.02x1.46 in)</li>
	 * <li>A11 (18x26 mm ; 0.71x1.02 in)</li>
	 * <li>A12 (13x18 mm ; 0.51x0.71 in)</li>
	 * <li><b>ISO 216 B Series + 2 SIS 014711 extensions</b></li>
	 * <li>B0 (1000x1414 mm ; 39.37x55.67 in)</li>
	 * <li>B1 (707x1000 mm ; 27.83x39.37 in)</li>
	 * <li>B2 (500x707 mm ; 19.69x27.83 in)</li>
	 * <li>B3 (353x500 mm ; 13.90x19.69 in)</li>
	 * <li>B4 (250x353 mm ; 9.84x13.90 in)</li>
	 * <li>B5 (176x250 mm ; 6.93x9.84 in)</li>
	 * <li>B6 (125x176 mm ; 4.92x6.93 in)</li>
	 * <li>B7 (88x125 mm ; 3.46x4.92 in)</li>
	 * <li>B8 (62x88 mm ; 2.44x3.46 in)</li>
	 * <li>B9 (44x62 mm ; 1.73x2.44 in)</li>
	 * <li>B10 (31x44 mm ; 1.22x1.73 in)</li>
	 * <li>B11 (22x31 mm ; 0.87x1.22 in)</li>
	 * <li>B12 (15x22 mm ; 0.59x0.87 in)</li>
	 * <li><b>ISO 216 C Series + 2 SIS 014711 extensions + 2 EXTENSION</b></li>
	 * <li>C0 (917x1297 mm ; 36.10x51.06 in)</li>
	 * <li>C1 (648x917 mm ; 25.51x36.10 in)</li>
	 * <li>C2 (458x648 mm ; 18.03x25.51 in)</li>
	 * <li>C3 (324x458 mm ; 12.76x18.03 in)</li>
	 * <li>C4 (229x324 mm ; 9.02x12.76 in)</li>
	 * <li>C5 (162x229 mm ; 6.38x9.02 in)</li>
	 * <li>C6 (114x162 mm ; 4.49x6.38 in)</li>
	 * <li>C7 (81x114 mm ; 3.19x4.49 in)</li>
	 * <li>C8 (57x81 mm ; 2.24x3.19 in)</li>
	 * <li>C9 (40x57 mm ; 1.57x2.24 in)</li>
	 * <li>C10 (28x40 mm ; 1.10x1.57 in)</li>
	 * <li>C11 (20x28 mm ; 0.79x1.10 in)</li>
	 * <li>C12 (14x20 mm ; 0.55x0.79 in)</li>
	 * <li>C76 (81x162 mm ; 3.19x6.38 in)</li>
	 * <li>DL (110x220 mm ; 4.33x8.66 in)</li>
	 * <li><b>SIS 014711 E Series</b></li>
	 * <li>E0 (879x1241 mm ; 34.61x48.86 in)</li>
	 * <li>E1 (620x879 mm ; 24.41x34.61 in)</li>
	 * <li>E2 (440x620 mm ; 17.32x24.41 in)</li>
	 * <li>E3 (310x440 mm ; 12.20x17.32 in)</li>
	 * <li>E4 (220x310 mm ; 8.66x12.20 in)</li>
	 * <li>E5 (155x220 mm ; 6.10x8.66 in)</li>
	 * <li>E6 (110x155 mm ; 4.33x6.10 in)</li>
	 * <li>E7 (78x110 mm ; 3.07x4.33 in)</li>
	 * <li>E8 (55x78 mm ; 2.17x3.07 in)</li>
	 * <li>E9 (39x55 mm ; 1.54x2.17 in)</li>
	 * <li>E10 (27x39 mm ; 1.06x1.54 in)</li>
	 * <li>E11 (19x27 mm ; 0.75x1.06 in)</li>
	 * <li>E12 (13x19 mm ; 0.51x0.75 in)</li>
	 * <li><b>SIS 014711 G Series</b></li>
	 * <li>G0 (958x1354 mm ; 37.72x53.31 in)</li>
	 * <li>G1 (677x958 mm ; 26.65x37.72 in)</li>
	 * <li>G2 (479x677 mm ; 18.86x26.65 in)</li>
	 * <li>G3 (338x479 mm ; 13.31x18.86 in)</li>
	 * <li>G4 (239x338 mm ; 9.41x13.31 in)</li>
	 * <li>G5 (169x239 mm ; 6.65x9.41 in)</li>
	 * <li>G6 (119x169 mm ; 4.69x6.65 in)</li>
	 * <li>G7 (84x119 mm ; 3.31x4.69 in)</li>
	 * <li>G8 (59x84 mm ; 2.32x3.31 in)</li>
	 * <li>G9 (42x59 mm ; 1.65x2.32 in)</li>
	 * <li>G10 (29x42 mm ; 1.14x1.65 in)</li>
	 * <li>G11 (21x29 mm ; 0.83x1.14 in)</li>
	 * <li>G12 (14x21 mm ; 0.55x0.83 in)</li>
	 * <li><b>ISO Press</b></li>
	 * <li>RA0 (860x1220 mm ; 33.86x48.03 in)</li>
	 * <li>RA1 (610x860 mm ; 24.02x33.86 in)</li>
	 * <li>RA2 (430x610 mm ; 16.93x24.02 in)</li>
	 * <li>RA3 (305x430 mm ; 12.01x16.93 in)</li>
	 * <li>RA4 (215x305 mm ; 8.46x12.01 in)</li>
	 * <li>SRA0 (900x1280 mm ; 35.43x50.39 in)</li>
	 * <li>SRA1 (640x900 mm ; 25.20x35.43 in)</li>
	 * <li>SRA2 (450x640 mm ; 17.72x25.20 in)</li>
	 * <li>SRA3 (320x450 mm ; 12.60x17.72 in)</li>
	 * <li>SRA4 (225x320 mm ; 8.86x12.60 in)</li>
	 * <li><b>German DIN 476</b></li>
	 * <li>4A0 (1682x2378 mm ; 66.22x93.62 in)</li>
	 * <li>2A0 (1189x1682 mm ; 46.81x66.22 in)</li>
	 * <li><b>Variations on the ISO Standard</b></li>
	 * <li>A2_EXTRA (445x619 mm ; 17.52x24.37 in)</li>
	 * <li>A3+ (329x483 mm ; 12.95x19.02 in)</li>
	 * <li>A3_EXTRA (322x445 mm ; 12.68x17.52 in)</li>
	 * <li>A3_SUPER (305x508 mm ; 12.01x20.00 in)</li>
	 * <li>SUPER_A3 (305x487 mm ; 12.01x19.17 in)</li>
	 * <li>A4_EXTRA (235x322 mm ; 9.25x12.68 in)</li>
	 * <li>A4_SUPER (229x322 mm ; 9.02x12.68 in)</li>
	 * <li>SUPER_A4 (227x356 mm ; 8.94x14.02 in)</li>
	 * <li>A4_LONG (210x348 mm ; 8.27x13.70 in)</li>
	 * <li>F4 (210x330 mm ; 8.27x12.99 in)</li>
	 * <li>SO_B5_EXTRA (202x276 mm ; 7.95x10.87 in)</li>
	 * <li>A5_EXTRA (173x235 mm ; 6.81x9.25 in)</li>
	 * <li><b>ANSI Series</b></li>
	 * <li>ANSI_E (864x1118 mm ; 34.00x44.00 in)</li>
	 * <li>ANSI_D (559x864 mm ; 22.00x34.00 in)</li>
	 * <li>ANSI_C (432x559 mm ; 17.00x22.00 in)</li>
	 * <li>ANSI_B (279x432 mm ; 11.00x17.00 in)</li>
	 * <li>ANSI_A (216x279 mm ; 8.50x11.00 in)</li>
	 * <li><b>Traditional 'Loose' North American Paper Sizes</b></li>
	 * <li>LEDGER, USLEDGER (432x279 mm ; 17.00x11.00 in)</li>
	 * <li>TABLOID, USTABLOID, BIBLE, ORGANIZERK (279x432 mm ; 11.00x17.00 in)</li>
	 * <li>LETTER, USLETTER, ORGANIZERM (216x279 mm ; 8.50x11.00 in)</li>
	 * <li>LEGAL, USLEGAL (216x356 mm ; 8.50x14.00 in)</li>
	 * <li>GLETTER, GOVERNMENTLETTER (203x267 mm ; 8.00x10.50 in)</li>
	 * <li>JLEGAL, JUNIORLEGAL (203x127 mm ; 8.00x5.00 in)</li>
	 * <li><b>Other North American Paper Sizes</b></li>
	 * <li>QUADDEMY (889x1143 mm ; 35.00x45.00 in)</li>
	 * <li>SUPER_B (330x483 mm ; 13.00x19.00 in)</li>
	 * <li>QUARTO (229x279 mm ; 9.00x11.00 in)</li>
	 * <li>FOLIO, GOVERNMENTLEGAL (216x330 mm ; 8.50x13.00 in)</li>
	 * <li>EXECUTIVE, MONARCH (184x267 mm ; 7.25x10.50 in)</li>
	 * <li>MEMO, STATEMENT, ORGANIZERL (140x216 mm ; 5.50x8.50 in)</li>
	 * <li>FOOLSCAP (210x330 mm ; 8.27x13.00 in)</li>
	 * <li>COMPACT (108x171 mm ; 4.25x6.75 in)</li>
	 * <li>ORGANIZERJ (70x127 mm ; 2.75x5.00 in)</li>
	 * <li><b>Canadian standard CAN 2-9.60M</b></li>
	 * <li>P1 (560x860 mm ; 22.05x33.86 in)</li>
	 * <li>P2 (430x560 mm ; 16.93x22.05 in)</li>
	 * <li>P3 (280x430 mm ; 11.02x16.93 in)</li>
	 * <li>P4 (215x280 mm ; 8.46x11.02 in)</li>
	 * <li>P5 (140x215 mm ; 5.51x8.46 in)</li>
	 * <li>P6 (107x140 mm ; 4.21x5.51 in)</li>
	 * <li><b>North American Architectural Sizes</b></li>
	 * <li>ARCH_E (914x1219 mm ; 36.00x48.00 in)</li>
	 * <li>ARCH_E1 (762x1067 mm ; 30.00x42.00 in)</li>
	 * <li>ARCH_D (610x914 mm ; 24.00x36.00 in)</li>
	 * <li>ARCH_C, BROADSHEET (457x610 mm ; 18.00x24.00 in)</li>
	 * <li>ARCH_B (305x457 mm ; 12.00x18.00 in)</li>
	 * <li>ARCH_A (229x305 mm ; 9.00x12.00 in)</li>
	 * <li><b>Announcement Envelopes</b></li>
	 * <li>ANNENV_A2 (111x146 mm ; 4.37x5.75 in)</li>
	 * <li>ANNENV_A6 (121x165 mm ; 4.75x6.50 in)</li>
	 * <li>ANNENV_A7 (133x184 mm ; 5.25x7.25 in)</li>
	 * <li>ANNENV_A8 (140x206 mm ; 5.50x8.12 in)</li>
	 * <li>ANNENV_A10 (159x244 mm ; 6.25x9.62 in)</li>
	 * <li>ANNENV_SLIM (98x225 mm ; 3.87x8.87 in)</li>
	 * <li><b>Commercial Envelopes</b></li>
	 * <li>COMMENV_N6_1/4 (89x152 mm ; 3.50x6.00 in)</li>
	 * <li>COMMENV_N6_3/4 (92x165 mm ; 3.62x6.50 in)</li>
	 * <li>COMMENV_N8 (98x191 mm ; 3.87x7.50 in)</li>
	 * <li>COMMENV_N9 (98x225 mm ; 3.87x8.87 in)</li>
	 * <li>COMMENV_N10 (105x241 mm ; 4.12x9.50 in)</li>
	 * <li>COMMENV_N11 (114x263 mm ; 4.50x10.37 in)</li>
	 * <li>COMMENV_N12 (121x279 mm ; 4.75x11.00 in)</li>
	 * <li>COMMENV_N14 (127x292 mm ; 5.00x11.50 in)</li>
	 * <li><b>Catalogue Envelopes</b></li>
	 * <li>CATENV_N1 (152x229 mm ; 6.00x9.00 in)</li>
	 * <li>CATENV_N1_3/4 (165x241 mm ; 6.50x9.50 in)</li>
	 * <li>CATENV_N2 (165x254 mm ; 6.50x10.00 in)</li>
	 * <li>CATENV_N3 (178x254 mm ; 7.00x10.00 in)</li>
	 * <li>CATENV_N6 (191x267 mm ; 7.50x10.50 in)</li>
	 * <li>CATENV_N7 (203x279 mm ; 8.00x11.00 in)</li>
	 * <li>CATENV_N8 (210x286 mm ; 8.25x11.25 in)</li>
	 * <li>CATENV_N9_1/2 (216x267 mm ; 8.50x10.50 in)</li>
	 * <li>CATENV_N9_3/4 (222x286 mm ; 8.75x11.25 in)</li>
	 * <li>CATENV_N10_1/2 (229x305 mm ; 9.00x12.00 in)</li>
	 * <li>CATENV_N12_1/2 (241x318 mm ; 9.50x12.50 in)</li>
	 * <li>CATENV_N13_1/2 (254x330 mm ; 10.00x13.00 in)</li>
	 * <li>CATENV_N14_1/4 (286x311 mm ; 11.25x12.25 in)</li>
	 * <li>CATENV_N14_1/2 (292x368 mm ; 11.50x14.50 in)</li>
	 * <li><b>Japanese (JIS P 0138-61) Standard B-Series</b></li>
	 * <li>JIS_B0 (1030x1456 mm ; 40.55x57.32 in)</li>
	 * <li>JIS_B1 (728x1030 mm ; 28.66x40.55 in)</li>
	 * <li>JIS_B2 (515x728 mm ; 20.28x28.66 in)</li>
	 * <li>JIS_B3 (364x515 mm ; 14.33x20.28 in)</li>
	 * <li>JIS_B4 (257x364 mm ; 10.12x14.33 in)</li>
	 * <li>JIS_B5 (182x257 mm ; 7.17x10.12 in)</li>
	 * <li>JIS_B6 (128x182 mm ; 5.04x7.17 in)</li>
	 * <li>JIS_B7 (91x128 mm ; 3.58x5.04 in)</li>
	 * <li>JIS_B8 (64x91 mm ; 2.52x3.58 in)</li>
	 * <li>JIS_B9 (45x64 mm ; 1.77x2.52 in)</li>
	 * <li>JIS_B10 (32x45 mm ; 1.26x1.77 in)</li>
	 * <li>JIS_B11 (22x32 mm ; 0.87x1.26 in)</li>
	 * <li>JIS_B12 (16x22 mm ; 0.63x0.87 in)</li>
	 * <li><b>PA Series</b></li>
	 * <li>PA0 (840x1120 mm ; 33.07x44.09 in)</li>
	 * <li>PA1 (560x840 mm ; 22.05x33.07 in)</li>
	 * <li>PA2 (420x560 mm ; 16.54x22.05 in)</li>
	 * <li>PA3 (280x420 mm ; 11.02x16.54 in)</li>
	 * <li>PA4 (210x280 mm ; 8.27x11.02 in)</li>
	 * <li>PA5 (140x210 mm ; 5.51x8.27 in)</li>
	 * <li>PA6 (105x140 mm ; 4.13x5.51 in)</li>
	 * <li>PA7 (70x105 mm ; 2.76x4.13 in)</li>
	 * <li>PA8 (52x70 mm ; 2.05x2.76 in)</li>
	 * <li>PA9 (35x52 mm ; 1.38x2.05 in)</li>
	 * <li>PA10 (26x35 mm ; 1.02x1.38 in)</li>
	 * <li><b>Standard Photographic Print Sizes</b></li>
	 * <li>PASSPORT_PHOTO (35x45 mm ; 1.38x1.77 in)</li>
	 * <li>E (82x120 mm ; 3.25x4.72 in)</li>
	 * <li>3R, L (89x127 mm ; 3.50x5.00 in)</li>
	 * <li>4R, KG (102x152 mm ; 4.02x5.98 in)</li>
	 * <li>4D (120x152 mm ; 4.72x5.98 in)</li>
	 * <li>5R, 2L (127x178 mm ; 5.00x7.01 in)</li>
	 * <li>6R, 8P (152x203 mm ; 5.98x7.99 in)</li>
	 * <li>8R, 6P (203x254 mm ; 7.99x10.00 in)</li>
	 * <li>S8R, 6PW (203x305 mm ; 7.99x12.01 in)</li>
	 * <li>10R, 4P (254x305 mm ; 10.00x12.01 in)</li>
	 * <li>S10R, 4PW (254x381 mm ; 10.00x15.00 in)</li>
	 * <li>11R (279x356 mm ; 10.98x14.02 in)</li>
	 * <li>S11R (279x432 mm ; 10.98x17.01 in)</li>
	 * <li>12R (305x381 mm ; 12.01x15.00 in)</li>
	 * <li>S12R (305x456 mm ; 12.01x17.95 in)</li>
	 * <li><b>Common Newspaper Sizes</b></li>
	 * <li>NEWSPAPER_BROADSHEET (750x600 mm ; 29.53x23.62 in)</li>
	 * <li>NEWSPAPER_BERLINER (470x315 mm ; 18.50x12.40 in)</li>
	 * <li>NEWSPAPER_COMPACT, NEWSPAPER_TABLOID (430x280 mm ; 16.93x11.02 in)</li>
	 * <li><b>Business Cards</b></li>
	 * <li>CREDIT_CARD, BUSINESS_CARD, BUSINESS_CARD_ISO7810 (54x86 mm ; 2.13x3.37 in)</li>
	 * <li>BUSINESS_CARD_ISO216 (52x74 mm ; 2.05x2.91 in)</li>
	 * <li>BUSINESS_CARD_IT, BUSINESS_CARD_UK, BUSINESS_CARD_FR, BUSINESS_CARD_DE, BUSINESS_CARD_ES (55x85 mm ; 2.17x3.35 in)</li>
	 * <li>BUSINESS_CARD_US, BUSINESS_CARD_CA (51x89 mm ; 2.01x3.50 in)</li>
	 * <li>BUSINESS_CARD_JP (55x91 mm ; 2.17x3.58 in)</li>
	 * <li>BUSINESS_CARD_HK (54x90 mm ; 2.13x3.54 in)</li>
	 * <li>BUSINESS_CARD_AU, BUSINESS_CARD_DK, BUSINESS_CARD_SE (55x90 mm ; 2.17x3.54 in)</li>
	 * <li>BUSINESS_CARD_RU, BUSINESS_CARD_CZ, BUSINESS_CARD_FI, BUSINESS_CARD_HU, BUSINESS_CARD_IL (50x90 mm ; 1.97x3.54 in)</li>
	 * <li><b>Billboards</b></li>
	 * <li>4SHEET (1016x1524 mm ; 40.00x60.00 in)</li>
	 * <li>6SHEET (1200x1800 mm ; 47.24x70.87 in)</li>
	 * <li>12SHEET (3048x1524 mm ; 120.00x60.00 in)</li>
	 * <li>16SHEET (2032x3048 mm ; 80.00x120.00 in)</li>
	 * <li>32SHEET (4064x3048 mm ; 160.00x120.00 in)</li>
	 * <li>48SHEET (6096x3048 mm ; 240.00x120.00 in)</li>
	 * <li>64SHEET (8128x3048 mm ; 320.00x120.00 in)</li>
	 * <li>96SHEET (12192x3048 mm ; 480.00x120.00 in)</li>
	 * <li><b>Old Imperial English (some are still used in USA)</b></li>
	 * <li>EN_EMPEROR (1219x1829 mm ; 48.00x72.00 in)</li>
	 * <li>EN_ANTIQUARIAN (787x1346 mm ; 31.00x53.00 in)</li>
	 * <li>EN_GRAND_EAGLE (730x1067 mm ; 28.75x42.00 in)</li>
	 * <li>EN_DOUBLE_ELEPHANT (679x1016 mm ; 26.75x40.00 in)</li>
	 * <li>EN_ATLAS (660x864 mm ; 26.00x34.00 in)</li>
	 * <li>EN_COLOMBIER (597x876 mm ; 23.50x34.50 in)</li>
	 * <li>EN_ELEPHANT (584x711 mm ; 23.00x28.00 in)</li>
	 * <li>EN_DOUBLE_DEMY (572x902 mm ; 22.50x35.50 in)</li>
	 * <li>EN_IMPERIAL (559x762 mm ; 22.00x30.00 in)</li>
	 * <li>EN_PRINCESS (546x711 mm ; 21.50x28.00 in)</li>
	 * <li>EN_CARTRIDGE (533x660 mm ; 21.00x26.00 in)</li>
	 * <li>EN_DOUBLE_LARGE_POST (533x838 mm ; 21.00x33.00 in)</li>
	 * <li>EN_ROYAL (508x635 mm ; 20.00x25.00 in)</li>
	 * <li>EN_SHEET, EN_HALF_POST (495x597 mm ; 19.50x23.50 in)</li>
	 * <li>EN_SUPER_ROYAL (483x686 mm ; 19.00x27.00 in)</li>
	 * <li>EN_DOUBLE_POST (483x775 mm ; 19.00x30.50 in)</li>
	 * <li>EN_MEDIUM (445x584 mm ; 17.50x23.00 in)</li>
	 * <li>EN_DEMY (445x572 mm ; 17.50x22.50 in)</li>
	 * <li>EN_LARGE_POST (419x533 mm ; 16.50x21.00 in)</li>
	 * <li>EN_COPY_DRAUGHT (406x508 mm ; 16.00x20.00 in)</li>
	 * <li>EN_POST (394x489 mm ; 15.50x19.25 in)</li>
	 * <li>EN_CROWN (381x508 mm ; 15.00x20.00 in)</li>
	 * <li>EN_PINCHED_POST (375x470 mm ; 14.75x18.50 in)</li>
	 * <li>EN_BRIEF (343x406 mm ; 13.50x16.00 in)</li>
	 * <li>EN_FOOLSCAP (343x432 mm ; 13.50x17.00 in)</li>
	 * <li>EN_SMALL_FOOLSCAP (337x419 mm ; 13.25x16.50 in)</li>
	 * <li>EN_POTT (318x381 mm ; 12.50x15.00 in)</li>
	 * <li><b>Old Imperial Belgian</b></li>
	 * <li>BE_GRAND_AIGLE (700x1040 mm ; 27.56x40.94 in)</li>
	 * <li>BE_COLOMBIER (620x850 mm ; 24.41x33.46 in)</li>
	 * <li>BE_DOUBLE_CARRE (620x920 mm ; 24.41x36.22 in)</li>
	 * <li>BE_ELEPHANT (616x770 mm ; 24.25x30.31 in)</li>
	 * <li>BE_PETIT_AIGLE (600x840 mm ; 23.62x33.07 in)</li>
	 * <li>BE_GRAND_JESUS (550x730 mm ; 21.65x28.74 in)</li>
	 * <li>BE_JESUS (540x730 mm ; 21.26x28.74 in)</li>
	 * <li>BE_RAISIN (500x650 mm ; 19.69x25.59 in)</li>
	 * <li>BE_GRAND_MEDIAN (460x605 mm ; 18.11x23.82 in)</li>
	 * <li>BE_DOUBLE_POSTE (435x565 mm ; 17.13x22.24 in)</li>
	 * <li>BE_COQUILLE (430x560 mm ; 16.93x22.05 in)</li>
	 * <li>BE_PETIT_MEDIAN (415x530 mm ; 16.34x20.87 in)</li>
	 * <li>BE_RUCHE (360x460 mm ; 14.17x18.11 in)</li>
	 * <li>BE_PROPATRIA (345x430 mm ; 13.58x16.93 in)</li>
	 * <li>BE_LYS (317x397 mm ; 12.48x15.63 in)</li>
	 * <li>BE_POT (307x384 mm ; 12.09x15.12 in)</li>
	 * <li>BE_ROSETTE (270x347 mm ; 10.63x13.66 in)</li>
	 * <li><b>Old Imperial French</b></li>
	 * <li>FR_UNIVERS (1000x1300 mm ; 39.37x51.18 in)</li>
	 * <li>FR_DOUBLE_COLOMBIER (900x1260 mm ; 35.43x49.61 in)</li>
	 * <li>FR_GRANDE_MONDE (900x1260 mm ; 35.43x49.61 in)</li>
	 * <li>FR_DOUBLE_SOLEIL (800x1200 mm ; 31.50x47.24 in)</li>
	 * <li>FR_DOUBLE_JESUS (760x1120 mm ; 29.92x44.09 in)</li>
	 * <li>FR_GRAND_AIGLE (750x1060 mm ; 29.53x41.73 in)</li>
	 * <li>FR_PETIT_AIGLE (700x940 mm ; 27.56x37.01 in)</li>
	 * <li>FR_DOUBLE_RAISIN (650x1000 mm ; 25.59x39.37 in)</li>
	 * <li>FR_JOURNAL (650x940 mm ; 25.59x37.01 in)</li>
	 * <li>FR_COLOMBIER_AFFICHE (630x900 mm ; 24.80x35.43 in)</li>
	 * <li>FR_DOUBLE_CAVALIER (620x920 mm ; 24.41x36.22 in)</li>
	 * <li>FR_CLOCHE (600x800 mm ; 23.62x31.50 in)</li>
	 * <li>FR_SOLEIL (600x800 mm ; 23.62x31.50 in)</li>
	 * <li>FR_DOUBLE_CARRE (560x900 mm ; 22.05x35.43 in)</li>
	 * <li>FR_DOUBLE_COQUILLE (560x880 mm ; 22.05x34.65 in)</li>
	 * <li>FR_JESUS (560x760 mm ; 22.05x29.92 in)</li>
	 * <li>FR_RAISIN (500x650 mm ; 19.69x25.59 in)</li>
	 * <li>FR_CAVALIER (460x620 mm ; 18.11x24.41 in)</li>
	 * <li>FR_DOUBLE_COURONNE (460x720 mm ; 18.11x28.35 in)</li>
	 * <li>FR_CARRE (450x560 mm ; 17.72x22.05 in)</li>
	 * <li>FR_COQUILLE (440x560 mm ; 17.32x22.05 in)</li>
	 * <li>FR_DOUBLE_TELLIERE (440x680 mm ; 17.32x26.77 in)</li>
	 * <li>FR_DOUBLE_CLOCHE (400x600 mm ; 15.75x23.62 in)</li>
	 * <li>FR_DOUBLE_POT (400x620 mm ; 15.75x24.41 in)</li>
	 * <li>FR_ECU (400x520 mm ; 15.75x20.47 in)</li>
	 * <li>FR_COURONNE (360x460 mm ; 14.17x18.11 in)</li>
	 * <li>FR_TELLIERE (340x440 mm ; 13.39x17.32 in)</li>
	 * <li>FR_POT (310x400 mm ; 12.20x15.75 in)</li>
	 * </ul>
	 * @return array containing page width and height in points
	 * @access public
	 * @since 5.0.010 (2010-05-17)
	 */
	public function getPageSizeFromFormat($format) {
		// Paper cordinates are calculated in this way: (inches * 72) where (1 inch = 25.4 mm)
		switch (strtoupper($format)) {
			// ISO 216 A Series + 2 SIS 014711 extensions
			case 'A0' : {$pf = array( 2383.937, 3370.394); break;}
			case 'A1' : {$pf = array( 1683.780, 2383.937); break;}
			case 'A2' : {$pf = array( 1190.551, 1683.780); break;}
			case 'A3' : {$pf = array(  841.890, 1190.551); break;}
			case 'A4' : {$pf = array(  595.276,  841.890); break;}
			case 'A5' : {$pf = array(  419.528,  595.276); break;}
			case 'A6' : {$pf = array(  297.638,  419.528); break;}
			case 'A7' : {$pf = array(  209.764,  297.638); break;}
			case 'A8' : {$pf = array(  147.402,  209.764); break;}
			case 'A9' : {$pf = array(  104.882,  147.402); break;}
			case 'A10': {$pf = array(   73.701,  104.882); break;}
			case 'A11': {$pf = array(   51.024,   73.701); break;}
			case 'A12': {$pf = array(   36.850,   51.024); break;}
			// ISO 216 B Series + 2 SIS 014711 extensions
			case 'B0' : {$pf = array( 2834.646, 4008.189); break;}
			case 'B1' : {$pf = array( 2004.094, 2834.646); break;}
			case 'B2' : {$pf = array( 1417.323, 2004.094); break;}
			case 'B3' : {$pf = array( 1000.630, 1417.323); break;}
			case 'B4' : {$pf = array(  708.661, 1000.630); break;}
			case 'B5' : {$pf = array(  498.898,  708.661); break;}
			case 'B6' : {$pf = array(  354.331,  498.898); break;}
			case 'B7' : {$pf = array(  249.449,  354.331); break;}
			case 'B8' : {$pf = array(  175.748,  249.449); break;}
			case 'B9' : {$pf = array(  124.724,  175.748); break;}
			case 'B10': {$pf = array(   87.874,  124.724); break;}
			case 'B11': {$pf = array(   62.362,   87.874); break;}
			case 'B12': {$pf = array(   42.520,   62.362); break;}
			// ISO 216 C Series + 2 SIS 014711 extensions + 2 EXTENSION
			case 'C0' : {$pf = array( 2599.370, 3676.535); break;}
			case 'C1' : {$pf = array( 1836.850, 2599.370); break;}
			case 'C2' : {$pf = array( 1298.268, 1836.850); break;}
			case 'C3' : {$pf = array(  918.425, 1298.268); break;}
			case 'C4' : {$pf = array(  649.134,  918.425); break;}
			case 'C5' : {$pf = array(  459.213,  649.134); break;}
			case 'C6' : {$pf = array(  323.150,  459.213); break;}
			case 'C7' : {$pf = array(  229.606,  323.150); break;}
			case 'C8' : {$pf = array(  161.575,  229.606); break;}
			case 'C9' : {$pf = array(  113.386,  161.575); break;}
			case 'C10': {$pf = array(   79.370,  113.386); break;}
			case 'C11': {$pf = array(   56.693,   79.370); break;}
			case 'C12': {$pf = array(   39.685,   56.693); break;}
			case 'C76': {$pf = array(  229.606,  459.213); break;}
			case 'DL' : {$pf = array(  311.811,  623.622); break;}
			// SIS 014711 E Series
			case 'E0' : {$pf = array( 2491.654, 3517.795); break;}
			case 'E1' : {$pf = array( 1757.480, 2491.654); break;}
			case 'E2' : {$pf = array( 1247.244, 1757.480); break;}
			case 'E3' : {$pf = array(  878.740, 1247.244); break;}
			case 'E4' : {$pf = array(  623.622,  878.740); break;}
			case 'E5' : {$pf = array(  439.370,  623.622); break;}
			case 'E6' : {$pf = array(  311.811,  439.370); break;}
			case 'E7' : {$pf = array(  221.102,  311.811); break;}
			case 'E8' : {$pf = array(  155.906,  221.102); break;}
			case 'E9' : {$pf = array(  110.551,  155.906); break;}
			case 'E10': {$pf = array(   76.535,  110.551); break;}
			case 'E11': {$pf = array(   53.858,   76.535); break;}
			case 'E12': {$pf = array(   36.850,   53.858); break;}
			// SIS 014711 G Series
			case 'G0' : {$pf = array( 2715.591, 3838.110); break;}
			case 'G1' : {$pf = array( 1919.055, 2715.591); break;}
			case 'G2' : {$pf = array( 1357.795, 1919.055); break;}
			case 'G3' : {$pf = array(  958.110, 1357.795); break;}
			case 'G4' : {$pf = array(  677.480,  958.110); break;}
			case 'G5' : {$pf = array(  479.055,  677.480); break;}
			case 'G6' : {$pf = array(  337.323,  479.055); break;}
			case 'G7' : {$pf = array(  238.110,  337.323); break;}
			case 'G8' : {$pf = array(  167.244,  238.110); break;}
			case 'G9' : {$pf = array(  119.055,  167.244); break;}
			case 'G10': {$pf = array(   82.205,  119.055); break;}
			case 'G11': {$pf = array(   59.528,   82.205); break;}
			case 'G12': {$pf = array(   39.685,   59.528); break;}
			// ISO Press
			case 'RA0': {$pf = array( 2437.795, 3458.268); break;}
			case 'RA1': {$pf = array( 1729.134, 2437.795); break;}
			case 'RA2': {$pf = array( 1218.898, 1729.134); break;}
			case 'RA3': {$pf = array(  864.567, 1218.898); break;}
			case 'RA4': {$pf = array(  609.449,  864.567); break;}
			case 'SRA0': {$pf = array( 2551.181, 3628.346); break;}
			case 'SRA1': {$pf = array( 1814.173, 2551.181); break;}
			case 'SRA2': {$pf = array( 1275.591, 1814.173); break;}
			case 'SRA3': {$pf = array(  907.087, 1275.591); break;}
			case 'SRA4': {$pf = array(  637.795,  907.087); break;}
			// German  DIN 476
			case '4A0': {$pf = array( 4767.874, 6740.787); break;}
			case '2A0': {$pf = array( 3370.394, 4767.874); break;}
			// Variations on the ISO Standard
			case 'A2_EXTRA'   : {$pf = array( 1261.417, 1754.646); break;}
			case 'A3+'        : {$pf = array(  932.598, 1369.134); break;}
			case 'A3_EXTRA'   : {$pf = array(  912.756, 1261.417); break;}
			case 'A3_SUPER'   : {$pf = array(  864.567, 1440.000); break;}
			case 'SUPER_A3'   : {$pf = array(  864.567, 1380.472); break;}
			case 'A4_EXTRA'   : {$pf = array(  666.142,  912.756); break;}
			case 'A4_SUPER'   : {$pf = array(  649.134,  912.756); break;}
			case 'SUPER_A4'   : {$pf = array(  643.465, 1009.134); break;}
			case 'A4_LONG'    : {$pf = array(  595.276,  986.457); break;}
			case 'F4'         : {$pf = array(  595.276,  935.433); break;}
			case 'SO_B5_EXTRA': {$pf = array(  572.598,  782.362); break;}
			case 'A5_EXTRA'   : {$pf = array(  490.394,  666.142); break;}
			// ANSI Series
			case 'ANSI_E': {$pf = array( 2448.000, 3168.000); break;}
			case 'ANSI_D': {$pf = array( 1584.000, 2448.000); break;}
			case 'ANSI_C': {$pf = array( 1224.000, 1584.000); break;}
			case 'ANSI_B': {$pf = array(  792.000, 1224.000); break;}
			case 'ANSI_A': {$pf = array(  612.000,  792.000); break;}
			// Traditional 'Loose' North American Paper Sizes
			case 'USLEDGER':
			case 'LEDGER' : {$pf = array( 1224.000,  792.000); break;}
			case 'ORGANIZERK':
			case 'BIBLE':
			case 'USTABLOID':
			case 'TABLOID': {$pf = array(  792.000, 1224.000); break;}
			case 'ORGANIZERM':
			case 'USLETTER':
			case 'LETTER' : {$pf = array(  612.000,  792.000); break;}
			case 'USLEGAL':
			case 'LEGAL'  : {$pf = array(  612.000, 1008.000); break;}
			case 'GOVERNMENTLETTER':
			case 'GLETTER': {$pf = array(  576.000,  756.000); break;}
			case 'JUNIORLEGAL':
			case 'JLEGAL' : {$pf = array(  576.000,  360.000); break;}
			// Other North American Paper Sizes
			case 'QUADDEMY': {$pf = array( 2520.000, 3240.000); break;}
			case 'SUPER_B': {$pf = array(  936.000, 1368.000); break;}
			case 'QUARTO': {$pf = array(  648.000,  792.000); break;}
			case 'GOVERNMENTLEGAL':
			case 'FOLIO': {$pf = array(  612.000,  936.000); break;}
			case 'MONARCH':
			case 'EXECUTIVE': {$pf = array(  522.000,  756.000); break;}
			case 'ORGANIZERL':
			case 'STATEMENT':
			case 'MEMO': {$pf = array(  396.000,  612.000); break;}
			case 'FOOLSCAP': {$pf = array(  595.440,  936.000); break;}
			case 'COMPACT': {$pf = array(  306.000,  486.000); break;}
			case 'ORGANIZERJ': {$pf = array(  198.000,  360.000); break;}
			// Canadian standard CAN 2-9.60M
			case 'P1': {$pf = array( 1587.402, 2437.795); break;}
			case 'P2': {$pf = array( 1218.898, 1587.402); break;}
			case 'P3': {$pf = array(  793.701, 1218.898); break;}
			case 'P4': {$pf = array(  609.449,  793.701); break;}
			case 'P5': {$pf = array(  396.850,  609.449); break;}
			case 'P6': {$pf = array(  303.307,  396.850); break;}
			// North American Architectural Sizes
			case 'ARCH_E' : {$pf = array( 2592.000, 3456.000); break;}
			case 'ARCH_E1': {$pf = array( 2160.000, 3024.000); break;}
			case 'ARCH_D' : {$pf = array( 1728.000, 2592.000); break;}
			case 'BROADSHEET':
			case 'ARCH_C' : {$pf = array( 1296.000, 1728.000); break;}
			case 'ARCH_B' : {$pf = array(  864.000, 1296.000); break;}
			case 'ARCH_A' : {$pf = array(  648.000,  864.000); break;}
			// --- North American Envelope Sizes ---
			//   - Announcement Envelopes
			case 'ANNENV_A2'  : {$pf = array(  314.640,  414.000); break;}
			case 'ANNENV_A6'  : {$pf = array(  342.000,  468.000); break;}
			case 'ANNENV_A7'  : {$pf = array(  378.000,  522.000); break;}
			case 'ANNENV_A8'  : {$pf = array(  396.000,  584.640); break;}
			case 'ANNENV_A10' : {$pf = array(  450.000,  692.640); break;}
			case 'ANNENV_SLIM': {$pf = array(  278.640,  638.640); break;}
			//   - Commercial Envelopes
			case 'COMMENV_N6_1/4': {$pf = array(  252.000,  432.000); break;}
			case 'COMMENV_N6_3/4': {$pf = array(  260.640,  468.000); break;}
			case 'COMMENV_N8'    : {$pf = array(  278.640,  540.000); break;}
			case 'COMMENV_N9'    : {$pf = array(  278.640,  638.640); break;}
			case 'COMMENV_N10'   : {$pf = array(  296.640,  684.000); break;}
			case 'COMMENV_N11'   : {$pf = array(  324.000,  746.640); break;}
			case 'COMMENV_N12'   : {$pf = array(  342.000,  792.000); break;}
			case 'COMMENV_N14'   : {$pf = array(  360.000,  828.000); break;}
			//   - Catalogue Envelopes
			case 'CATENV_N1'     : {$pf = array(  432.000,  648.000); break;}
			case 'CATENV_N1_3/4' : {$pf = array(  468.000,  684.000); break;}
			case 'CATENV_N2'     : {$pf = array(  468.000,  720.000); break;}
			case 'CATENV_N3'     : {$pf = array(  504.000,  720.000); break;}
			case 'CATENV_N6'     : {$pf = array(  540.000,  756.000); break;}
			case 'CATENV_N7'     : {$pf = array(  576.000,  792.000); break;}
			case 'CATENV_N8'     : {$pf = array(  594.000,  810.000); break;}
			case 'CATENV_N9_1/2' : {$pf = array(  612.000,  756.000); break;}
			case 'CATENV_N9_3/4' : {$pf = array(  630.000,  810.000); break;}
			case 'CATENV_N10_1/2': {$pf = array(  648.000,  864.000); break;}
			case 'CATENV_N12_1/2': {$pf = array(  684.000,  900.000); break;}
			case 'CATENV_N13_1/2': {$pf = array(  720.000,  936.000); break;}
			case 'CATENV_N14_1/4': {$pf = array(  810.000,  882.000); break;}
			case 'CATENV_N14_1/2': {$pf = array(  828.000, 1044.000); break;}
			// Japanese (JIS P 0138-61) Standard B-Series
			case 'JIS_B0' : {$pf = array( 2919.685, 4127.244); break;}
			case 'JIS_B1' : {$pf = array( 2063.622, 2919.685); break;}
			case 'JIS_B2' : {$pf = array( 1459.843, 2063.622); break;}
			case 'JIS_B3' : {$pf = array( 1031.811, 1459.843); break;}
			case 'JIS_B4' : {$pf = array(  728.504, 1031.811); break;}
			case 'JIS_B5' : {$pf = array(  515.906,  728.504); break;}
			case 'JIS_B6' : {$pf = array(  362.835,  515.906); break;}
			case 'JIS_B7' : {$pf = array(  257.953,  362.835); break;}
			case 'JIS_B8' : {$pf = array(  181.417,  257.953); break;}
			case 'JIS_B9' : {$pf = array(  127.559,  181.417); break;}
			case 'JIS_B10': {$pf = array(   90.709,  127.559); break;}
			case 'JIS_B11': {$pf = array(   62.362,   90.709); break;}
			case 'JIS_B12': {$pf = array(   45.354,   62.362); break;}
			// PA Series
			case 'PA0' : {$pf = array( 2381.102, 3174.803,); break;}
			case 'PA1' : {$pf = array( 1587.402, 2381.102); break;}
			case 'PA2' : {$pf = array( 1190.551, 1587.402); break;}
			case 'PA3' : {$pf = array(  793.701, 1190.551); break;}
			case 'PA4' : {$pf = array(  595.276,  793.701); break;}
			case 'PA5' : {$pf = array(  396.850,  595.276); break;}
			case 'PA6' : {$pf = array(  297.638,  396.850); break;}
			case 'PA7' : {$pf = array(  198.425,  297.638); break;}
			case 'PA8' : {$pf = array(  147.402,  198.425); break;}
			case 'PA9' : {$pf = array(   99.213,  147.402); break;}
			case 'PA10': {$pf = array(   73.701,   99.213); break;}
			// Standard Photographic Print Sizes
			case 'PASSPORT_PHOTO': {$pf = array(   99.213,  127.559); break;}
			case 'E'   : {$pf = array(  233.858,  340.157); break;}
			case 'L':
			case '3R'  : {$pf = array(  252.283,  360.000); break;}
			case 'KG':
			case '4R'  : {$pf = array(  289.134,  430.866); break;}
			case '4D'  : {$pf = array(  340.157,  430.866); break;}
			case '2L':
			case '5R'  : {$pf = array(  360.000,  504.567); break;}
			case '8P':
			case '6R'  : {$pf = array(  430.866,  575.433); break;}
			case '6P':
			case '8R'  : {$pf = array(  575.433,  720.000); break;}
			case '6PW':
			case 'S8R' : {$pf = array(  575.433,  864.567); break;}
			case '4P':
			case '10R' : {$pf = array(  720.000,  864.567); break;}
			case '4PW':
			case 'S10R': {$pf = array(  720.000, 1080.000); break;}
			case '11R' : {$pf = array(  790.866, 1009.134); break;}
			case 'S11R': {$pf = array(  790.866, 1224.567); break;}
			case '12R' : {$pf = array(  864.567, 1080.000); break;}
			case 'S12R': {$pf = array(  864.567, 1292.598); break;}
			// Common Newspaper Sizes
			case 'NEWSPAPER_BROADSHEET': {$pf = array( 2125.984, 1700.787); break;}
			case 'NEWSPAPER_BERLINER'  : {$pf = array( 1332.283,  892.913); break;}
			case 'NEWSPAPER_TABLOID':
			case 'NEWSPAPER_COMPACT'   : {$pf = array( 1218.898,  793.701); break;}
			// Business Cards
			case 'CREDIT_CARD':
			case 'BUSINESS_CARD':
			case 'BUSINESS_CARD_ISO7810': {$pf = array(  153.014,  242.646); break;}
			case 'BUSINESS_CARD_ISO216' : {$pf = array(  147.402,  209.764); break;}
			case 'BUSINESS_CARD_IT':
			case 'BUSINESS_CARD_UK':
			case 'BUSINESS_CARD_FR':
			case 'BUSINESS_CARD_DE':
			case 'BUSINESS_CARD_ES'     : {$pf = array(  155.906,  240.945); break;}
			case 'BUSINESS_CARD_CA':
			case 'BUSINESS_CARD_US'     : {$pf = array(  144.567,  252.283); break;}
			case 'BUSINESS_CARD_JP'     : {$pf = array(  155.906,  257.953); break;}
			case 'BUSINESS_CARD_HK'     : {$pf = array(  153.071,  255.118); break;}
			case 'BUSINESS_CARD_AU':
			case 'BUSINESS_CARD_DK':
			case 'BUSINESS_CARD_SE'     : {$pf = array(  155.906,  255.118); break;}
			case 'BUSINESS_CARD_RU':
			case 'BUSINESS_CARD_CZ':
			case 'BUSINESS_CARD_FI':
			case 'BUSINESS_CARD_HU':
			case 'BUSINESS_CARD_IL'     : {$pf = array(  141.732,  255.118); break;}
			// Billboards
			case '4SHEET' : {$pf = array( 2880.000, 4320.000); break;}
			case '6SHEET' : {$pf = array( 3401.575, 5102.362); break;}
			case '12SHEET': {$pf = array( 8640.000, 4320.000); break;}
			case '16SHEET': {$pf = array( 5760.000, 8640.000); break;}
			case '32SHEET': {$pf = array(11520.000, 8640.000); break;}
			case '48SHEET': {$pf = array(17280.000, 8640.000); break;}
			case '64SHEET': {$pf = array(23040.000, 8640.000); break;}
			case '96SHEET': {$pf = array(34560.000, 8640.000); break;}
			// Old European Sizes
			//   - Old Imperial English Sizes
			case 'EN_EMPEROR'          : {$pf = array( 3456.000, 5184.000); break;}
			case 'EN_ANTIQUARIAN'      : {$pf = array( 2232.000, 3816.000); break;}
			case 'EN_GRAND_EAGLE'      : {$pf = array( 2070.000, 3024.000); break;}
			case 'EN_DOUBLE_ELEPHANT'  : {$pf = array( 1926.000, 2880.000); break;}
			case 'EN_ATLAS'            : {$pf = array( 1872.000, 2448.000); break;}
			case 'EN_COLOMBIER'        : {$pf = array( 1692.000, 2484.000); break;}
			case 'EN_ELEPHANT'         : {$pf = array( 1656.000, 2016.000); break;}
			case 'EN_DOUBLE_DEMY'      : {$pf = array( 1620.000, 2556.000); break;}
			case 'EN_IMPERIAL'         : {$pf = array( 1584.000, 2160.000); break;}
			case 'EN_PRINCESS'         : {$pf = array( 1548.000, 2016.000); break;}
			case 'EN_CARTRIDGE'        : {$pf = array( 1512.000, 1872.000); break;}
			case 'EN_DOUBLE_LARGE_POST': {$pf = array( 1512.000, 2376.000); break;}
			case 'EN_ROYAL'            : {$pf = array( 1440.000, 1800.000); break;}
			case 'EN_SHEET':
			case 'EN_HALF_POST'        : {$pf = array( 1404.000, 1692.000); break;}
			case 'EN_SUPER_ROYAL'      : {$pf = array( 1368.000, 1944.000); break;}
			case 'EN_DOUBLE_POST'      : {$pf = array( 1368.000, 2196.000); break;}
			case 'EN_MEDIUM'           : {$pf = array( 1260.000, 1656.000); break;}
			case 'EN_DEMY'             : {$pf = array( 1260.000, 1620.000); break;}
			case 'EN_LARGE_POST'       : {$pf = array( 1188.000, 1512.000); break;}
			case 'EN_COPY_DRAUGHT'     : {$pf = array( 1152.000, 1440.000); break;}
			case 'EN_POST'             : {$pf = array( 1116.000, 1386.000); break;}
			case 'EN_CROWN'            : {$pf = array( 1080.000, 1440.000); break;}
			case 'EN_PINCHED_POST'     : {$pf = array( 1062.000, 1332.000); break;}
			case 'EN_BRIEF'            : {$pf = array(  972.000, 1152.000); break;}
			case 'EN_FOOLSCAP'         : {$pf = array(  972.000, 1224.000); break;}
			case 'EN_SMALL_FOOLSCAP'   : {$pf = array(  954.000, 1188.000); break;}
			case 'EN_POTT'             : {$pf = array(  900.000, 1080.000); break;}
			//   - Old Imperial Belgian Sizes
			case 'BE_GRAND_AIGLE' : {$pf = array( 1984.252, 2948.031); break;}
			case 'BE_COLOMBIER'   : {$pf = array( 1757.480, 2409.449); break;}
			case 'BE_DOUBLE_CARRE': {$pf = array( 1757.480, 2607.874); break;}
			case 'BE_ELEPHANT'    : {$pf = array( 1746.142, 2182.677); break;}
			case 'BE_PETIT_AIGLE' : {$pf = array( 1700.787, 2381.102); break;}
			case 'BE_GRAND_JESUS' : {$pf = array( 1559.055, 2069.291); break;}
			case 'BE_JESUS'       : {$pf = array( 1530.709, 2069.291); break;}
			case 'BE_RAISIN'      : {$pf = array( 1417.323, 1842.520); break;}
			case 'BE_GRAND_MEDIAN': {$pf = array( 1303.937, 1714.961); break;}
			case 'BE_DOUBLE_POSTE': {$pf = array( 1233.071, 1601.575); break;}
			case 'BE_COQUILLE'    : {$pf = array( 1218.898, 1587.402); break;}
			case 'BE_PETIT_MEDIAN': {$pf = array( 1176.378, 1502.362); break;}
			case 'BE_RUCHE'       : {$pf = array( 1020.472, 1303.937); break;}
			case 'BE_PROPATRIA'   : {$pf = array(  977.953, 1218.898); break;}
			case 'BE_LYS'         : {$pf = array(  898.583, 1125.354); break;}
			case 'BE_POT'         : {$pf = array(  870.236, 1088.504); break;}
			case 'BE_ROSETTE'     : {$pf = array(  765.354,  983.622); break;}
			//   - Old Imperial French Sizes
			case 'FR_UNIVERS'          : {$pf = array( 2834.646, 3685.039); break;}
			case 'FR_DOUBLE_COLOMBIER' : {$pf = array( 2551.181, 3571.654); break;}
			case 'FR_GRANDE_MONDE'     : {$pf = array( 2551.181, 3571.654); break;}
			case 'FR_DOUBLE_SOLEIL'    : {$pf = array( 2267.717, 3401.575); break;}
			case 'FR_DOUBLE_JESUS'     : {$pf = array( 2154.331, 3174.803); break;}
			case 'FR_GRAND_AIGLE'      : {$pf = array( 2125.984, 3004.724); break;}
			case 'FR_PETIT_AIGLE'      : {$pf = array( 1984.252, 2664.567); break;}
			case 'FR_DOUBLE_RAISIN'    : {$pf = array( 1842.520, 2834.646); break;}
			case 'FR_JOURNAL'          : {$pf = array( 1842.520, 2664.567); break;}
			case 'FR_COLOMBIER_AFFICHE': {$pf = array( 1785.827, 2551.181); break;}
			case 'FR_DOUBLE_CAVALIER'  : {$pf = array( 1757.480, 2607.874); break;}
			case 'FR_CLOCHE'           : {$pf = array( 1700.787, 2267.717); break;}
			case 'FR_SOLEIL'           : {$pf = array( 1700.787, 2267.717); break;}
			case 'FR_DOUBLE_CARRE'     : {$pf = array( 1587.402, 2551.181); break;}
			case 'FR_DOUBLE_COQUILLE'  : {$pf = array( 1587.402, 2494.488); break;}
			case 'FR_JESUS'            : {$pf = array( 1587.402, 2154.331); break;}
			case 'FR_RAISIN'           : {$pf = array( 1417.323, 1842.520); break;}
			case 'FR_CAVALIER'         : {$pf = array( 1303.937, 1757.480); break;}
			case 'FR_DOUBLE_COURONNE'  : {$pf = array( 1303.937, 2040.945); break;}
			case 'FR_CARRE'            : {$pf = array( 1275.591, 1587.402); break;}
			case 'FR_COQUILLE'         : {$pf = array( 1247.244, 1587.402); break;}
			case 'FR_DOUBLE_TELLIERE'  : {$pf = array( 1247.244, 1927.559); break;}
			case 'FR_DOUBLE_CLOCHE'    : {$pf = array( 1133.858, 1700.787); break;}
			case 'FR_DOUBLE_POT'       : {$pf = array( 1133.858, 1757.480); break;}
			case 'FR_ECU'              : {$pf = array( 1133.858, 1474.016); break;}
			case 'FR_COURONNE'         : {$pf = array( 1020.472, 1303.937); break;}
			case 'FR_TELLIERE'         : {$pf = array(  963.780, 1247.244); break;}
			case 'FR_POT'              : {$pf = array(  878.740, 1133.858); break;}
			// DEFAULT ISO A4
			default: {$pf = array(  595.276,  841.890); break;}
		}
		return $pf;
	}

	/**
	 * Change the format of the current page
	 * @param mixed $format The format used for pages. It can be either: one of the string values specified at getPageSizeFromFormat() documentation or an array of two numners (width, height) or an array containing the following measures and options:<ul>
	 * <li>['format'] = page format name (one of the above);</li>
	 * <li>['Rotate'] : The number of degrees by which the page shall be rotated clockwise when displayed or printed. The value shall be a multiple of 90.</li>
	 * <li>['PZ'] : The page's preferred zoom (magnification) factor.</li>
	 * <li>['MediaBox'] : the boundaries of the physical medium on which the page shall be displayed or printed:</li>
	 * <li>['MediaBox']['llx'] : lower-left x coordinate in points</li>
	 * <li>['MediaBox']['lly'] : lower-left y coordinate in points</li>
	 * <li>['MediaBox']['urx'] : upper-right x coordinate in points</li>
	 * <li>['MediaBox']['ury'] : upper-right y coordinate in points</li>
	 * <li>['CropBox'] : the visible region of default user space:</li>
	 * <li>['CropBox']['llx'] : lower-left x coordinate in points</li>
	 * <li>['CropBox']['lly'] : lower-left y coordinate in points</li>
	 * <li>['CropBox']['urx'] : upper-right x coordinate in points</li>
	 * <li>['CropBox']['ury'] : upper-right y coordinate in points</li>
	 * <li>['BleedBox'] : the region to which the contents of the page shall be clipped when output in a production environment:</li>
	 * <li>['BleedBox']['llx'] : lower-left x coordinate in points</li>
	 * <li>['BleedBox']['lly'] : lower-left y coordinate in points</li>
	 * <li>['BleedBox']['urx'] : upper-right x coordinate in points</li>
	 * <li>['BleedBox']['ury'] : upper-right y coordinate in points</li>
	 * <li>['TrimBox'] : the intended dimensions of the finished page after trimming:</li>
	 * <li>['TrimBox']['llx'] : lower-left x coordinate in points</li>
	 * <li>['TrimBox']['lly'] : lower-left y coordinate in points</li>
	 * <li>['TrimBox']['urx'] : upper-right x coordinate in points</li>
	 * <li>['TrimBox']['ury'] : upper-right y coordinate in points</li>
	 * <li>['ArtBox'] : the extent of the page's meaningful content:</li>
	 * <li>['ArtBox']['llx'] : lower-left x coordinate in points</li>
	 * <li>['ArtBox']['lly'] : lower-left y coordinate in points</li>
	 * <li>['ArtBox']['urx'] : upper-right x coordinate in points</li>
	 * <li>['ArtBox']['ury'] : upper-right y coordinate in points</li>
	 * <li>['BoxColorInfo'] :specify the colours and other visual characteristics that should be used in displaying guidelines on the screen for each of the possible page boundaries other than the MediaBox:</li>
	 * <li>['BoxColorInfo'][BOXTYPE]['C'] : an array of three numbers in the range 0-255, representing the components in the DeviceRGB colour space.</li>
	 * <li>['BoxColorInfo'][BOXTYPE]['W'] : the guideline width in default user units</li>
	 * <li>['BoxColorInfo'][BOXTYPE]['S'] : the guideline style: S = Solid; D = Dashed</li>
	 * <li>['BoxColorInfo'][BOXTYPE]['D'] : dash array defining a pattern of dashes and gaps to be used in drawing dashed guidelines</li>
	 * <li>['trans'] : the style and duration of the visual transition to use when moving from another page to the given page during a presentation</li>
	 * <li>['trans']['Dur'] : The page's display duration (also called its advance timing): the maximum length of time, in seconds, that the page shall be displayed during presentations before the viewer application shall automatically advance to the next page.</li>
	 * <li>['trans']['S'] : transition style : Split, Blinds, Box, Wipe, Dissolve, Glitter, R, Fly, Push, Cover, Uncover, Fade</li>
	 * <li>['trans']['D'] : The duration of the transition effect, in seconds.</li>
	 * <li>['trans']['Dm'] : (Split and Blinds transition styles only) The dimension in which the specified transition effect shall occur: H = Horizontal, V = Vertical. Default value: H.</li>
	 * <li>['trans']['M'] : (Split, Box and Fly transition styles only) The direction of motion for the specified transition effect: I = Inward from the edges of the page, O = Outward from the center of the pageDefault value: I.</li>
	 * <li>['trans']['Di'] : (Wipe, Glitter, Fly, Cover, Uncover and Push transition styles only) The direction in which the specified transition effect shall moves, expressed in degrees counterclockwise starting from a left-to-right direction. If the value is a number, it shall be one of: 0 = Left to right, 90 = Bottom to top (Wipe only), 180 = Right to left (Wipe only), 270 = Top to bottom, 315 = Top-left to bottom-right (Glitter only). If the value is a name, it shall be None, which is relevant only for the Fly transition when the value of SS is not 1.0. Default value: 0.</li>
	 * <li>['trans']['SS'] : (Fly transition style only) The starting or ending scale at which the changes shall be drawn. If M specifies an inward transition, the scale of the changes drawn shall progress from SS to 1.0 over the course of the transition. If M specifies an outward transition, the scale of the changes drawn shall progress from 1.0 to SS over the course of the transition. Default: 1.0.</li>
	 * <li>['trans']['B'] : (Fly transition style only) If true, the area that shall be flown in is rectangular and opaque. Default: false.</li>
	 * </ul>
	 * @param string $orientation page orientation. Possible values are (case insensitive):<ul>
	 * <li>P or Portrait (default)</li>
	 * <li>L or Landscape</li>
	 * <li>'' (empty string) for automatic orientation</li>
	 * </ul>
	 * @access protected
	 * @since 3.0.015 (2008-06-06)
	 * @see getPageSizeFromFormat()
	 */
	protected function setPageFormat($format, $orientation='P') {
		if (!empty($format) AND isset($this->pagedim[$this->page])) {
			// remove inherited values
			unset($this->pagedim[$this->page]);
		}
		if (is_string($format)) {
			// get page measures from format name
			$pf = $this->getPageSizeFromFormat($format);
			$this->fwPt = $pf[0];
			$this->fhPt = $pf[1];
		} else {
			// the boundaries of the physical medium on which the page shall be displayed or printed
			if (isset($format['MediaBox'])) {
				$this->setPageBoxes($this->page, 'MediaBox', $format['MediaBox']['llx'], $format['MediaBox']['lly'], $format['MediaBox']['urx'], $format['MediaBox']['ury'], false);
				$this->fwPt = (($format['MediaBox']['urx'] - $format['MediaBox']['llx']) * $this->k);
				$this->fhPt = (($format['MediaBox']['ury'] - $format['MediaBox']['lly']) * $this->k);
			} else {
				if (isset($format[0]) AND is_numeric($format[0]) AND isset($format[1]) AND is_numeric($format[1])) {
					$pf = array(($format[0] * $this->k), ($format[1] * $this->k));
				} else {
					if (!isset($format['format'])) {
						// default value
						$format['format'] = 'A4';
					}
					$pf = $this->getPageSizeFromFormat($format['format']);
				}
				$this->fwPt = $pf[0];
				$this->fhPt = $pf[1];
				$this->setPageBoxes($this->page, 'MediaBox', 0, 0, $this->fwPt, $this->fhPt, true);
			}
			// the visible region of default user space
			if (isset($format['CropBox'])) {
				$this->setPageBoxes($this->page, 'CropBox', $format['CropBox']['llx'], $format['CropBox']['lly'], $format['CropBox']['urx'], $format['CropBox']['ury'], false);
			}
			// the region to which the contents of the page shall be clipped when output in a production environment
			if (isset($format['BleedBox'])) {
				$this->setPageBoxes($this->page, 'BleedBox', $format['BleedBox']['llx'], $format['BleedBox']['lly'], $format['BleedBox']['urx'], $format['BleedBox']['ury'], false);
			}
			// the intended dimensions of the finished page after trimming
			if (isset($format['TrimBox'])) {
				$this->setPageBoxes($this->page, 'TrimBox', $format['TrimBox']['llx'], $format['TrimBox']['lly'], $format['TrimBox']['urx'], $format['TrimBox']['ury'], false);
			}
			// the page's meaningful content (including potential white space)
			if (isset($format['ArtBox'])) {
				$this->setPageBoxes($this->page, 'ArtBox', $format['ArtBox']['llx'], $format['ArtBox']['lly'], $format['ArtBox']['urx'], $format['ArtBox']['ury'], false);
			}
			// specify the colours and other visual characteristics that should be used in displaying guidelines on the screen for the various page boundaries
			if (isset($format['BoxColorInfo'])) {
				$this->pagedim[$this->page]['BoxColorInfo'] = $format['BoxColorInfo'];
			}
			if (isset($format['Rotate']) AND (($format['Rotate'] % 90) == 0)) {
				// The number of degrees by which the page shall be rotated clockwise when displayed or printed. The value shall be a multiple of 90.
				$this->pagedim[$this->page]['Rotate'] = intval($format['Rotate']);
			}
			if (isset($format['PZ'])) {
				// The page's preferred zoom (magnification) factor
				$this->pagedim[$this->page]['PZ'] = floatval($format['PZ']);
			}
			if (isset($format['trans'])) {
				// The style and duration of the visual transition to use when moving from another page to the given page during a presentation
				if (isset($format['trans']['Dur'])) {
					// The page's display duration
					$this->pagedim[$this->page]['trans']['Dur'] = floatval($format['trans']['Dur']);
				}
				$stansition_styles = array('Split', 'Blinds', 'Box', 'Wipe', 'Dissolve', 'Glitter', 'R', 'Fly', 'Push', 'Cover', 'Uncover', 'Fade');
				if (isset($format['trans']['S']) AND in_array($format['trans']['S'], $stansition_styles)) {
					// The transition style that shall be used when moving to this page from another during a presentation
					$this->pagedim[$this->page]['trans']['S'] = $format['trans']['S'];
					$valid_effect = array('Split', 'Blinds');
					$valid_vals = array('H', 'V');
					if (isset($format['trans']['Dm']) AND in_array($format['trans']['S'], $valid_effect) AND in_array($format['trans']['Dm'], $valid_vals)) {
						$this->pagedim[$this->page]['trans']['Dm'] = $format['trans']['Dm'];
					}
					$valid_effect = array('Split', 'Box', 'Fly');
					$valid_vals = array('I', 'O');
					if (isset($format['trans']['M']) AND in_array($format['trans']['S'], $valid_effect) AND in_array($format['trans']['M'], $valid_vals)) {
						$this->pagedim[$this->page]['trans']['M'] = $format['trans']['M'];
					}
					$valid_effect = array('Wipe', 'Glitter', 'Fly', 'Cover', 'Uncover', 'Push');
					if (isset($format['trans']['Di']) AND in_array($format['trans']['S'], $valid_effect)) {
						if (((($format['trans']['Di'] == 90) OR ($format['trans']['Di'] == 180)) AND ($format['trans']['S'] == 'Wipe'))
							OR (($format['trans']['Di'] == 315) AND ($format['trans']['S'] == 'Glitter'))
							OR (($format['trans']['Di'] == 0) OR ($format['trans']['Di'] == 270))) {
							$this->pagedim[$this->page]['trans']['Di'] = intval($format['trans']['Di']);
						}
					}
					if (isset($format['trans']['SS']) AND ($format['trans']['S'] == 'Fly')) {
						$this->pagedim[$this->page]['trans']['SS'] = floatval($format['trans']['SS']);
					}
					if (isset($format['trans']['B']) AND ($format['trans']['B'] === true) AND ($format['trans']['S'] == 'Fly')) {
						$this->pagedim[$this->page]['trans']['B'] = 'true';
					}
				} else {
					$this->pagedim[$this->page]['trans']['S'] = 'R';
				}
				if (isset($format['trans']['D'])) {
					// The duration of the transition effect, in seconds
					$this->pagedim[$this->page]['trans']['D'] = floatval($format['trans']['D']);
				} else {
					$this->pagedim[$this->page]['trans']['D'] = 1;
				}
			}
		}
		$this->setPageOrientation($orientation);
	}

	/**
	 * Set page boundaries.
	 * @param int $page page number
	 * @param string $type valid values are: <ul><li>'MediaBox' : the boundaries of the physical medium on which the page shall be displayed or printed;</li><li>'CropBox' : the visible region of default user space;</li><li>'BleedBox' : the region to which the contents of the page shall be clipped when output in a production environment;</li><li>'TrimBox' : the intended dimensions of the finished page after trimming;</li><li>'ArtBox' : the page's meaningful content (including potential white space).</li></ul>
	 * @param float $llx lower-left x coordinate in user units
	 * @param float $lly lower-left y coordinate in user units
	 * @param float $urx upper-right x coordinate in user units
	 * @param float $ury upper-right y coordinate in user units
	 * @param boolean $points if true uses user units as unit of measure, otherwise uses PDF points
	 * @access public
	 * @since 5.0.010 (2010-05-17)
	 */
	public function setPageBoxes($page, $type, $llx, $lly, $urx, $ury, $points=false) {
		if (!isset($this->pagedim[$page])) {
			// initialize array
			$this->pagedim[$page] = array();
		}
		$pageboxes = array('MediaBox', 'CropBox', 'BleedBox', 'TrimBox', 'ArtBox');
		if (!in_array($type, $pageboxes)) {
			return;
		}
		if ($points) {
			$k = 1;
		} else {
			$k = $this->k;
		}
		$this->pagedim[$page][$type]['llx'] = ($llx * $k);
		$this->pagedim[$page][$type]['lly'] = ($lly * $k);
		$this->pagedim[$page][$type]['urx'] = ($urx * $k);
		$this->pagedim[$page][$type]['ury'] = ($ury * $k);
	}

	/**
	 * Swap X and Y coordinates of page boxes (change page boxes orientation).
	 * @param int $page page number
	 * @access protected
	 * @since 5.0.010 (2010-05-17)
	 */
	protected function swapPageBoxCoordinates($page) {
		$pageboxes = array('MediaBox', 'CropBox', 'BleedBox', 'TrimBox', 'ArtBox');
		foreach ($pageboxes as $type) {
			// swap X and Y coordinates
			if (isset($this->pagedim[$page][$type])) {
				$tmp = $this->pagedim[$page][$type]['llx'];
				$this->pagedim[$page][$type]['llx'] = $this->pagedim[$page][$type]['lly'];
				$this->pagedim[$page][$type]['lly'] = $tmp;
				$tmp = $this->pagedim[$page][$type]['urx'];
				$this->pagedim[$page][$type]['urx'] = $this->pagedim[$page][$type]['ury'];
				$this->pagedim[$page][$type]['ury'] = $tmp;
			}
		}
	}

	/**
	 * Set page orientation.
	 * @param string $orientation page orientation. Possible values are (case insensitive):<ul><li>P or Portrait (default)</li><li>L or Landscape</li><li>'' (empty string) for automatic orientation</li></ul>
	 * @param boolean $autopagebreak Boolean indicating if auto-page-break mode should be on or off.
	 * @param float $bottommargin bottom margin of the page.
	 * @access public
	 * @since 3.0.015 (2008-06-06)
	 */
	public function setPageOrientation($orientation, $autopagebreak='', $bottommargin='') {
		if (!isset($this->pagedim[$this->page]['MediaBox'])) {
			// the boundaries of the physical medium on which the page shall be displayed or printed
			$this->setPageBoxes($this->page, 'MediaBox', 0, 0, $this->fwPt, $this->fhPt, true);
		}
		if (!isset($this->pagedim[$this->page]['CropBox'])) {
			// the visible region of default user space
			$this->setPageBoxes($this->page, 'CropBox', $this->pagedim[$this->page]['MediaBox']['llx'], $this->pagedim[$this->page]['MediaBox']['lly'], $this->pagedim[$this->page]['MediaBox']['urx'], $this->pagedim[$this->page]['MediaBox']['ury'], true);
		}
		if (!isset($this->pagedim[$this->page]['BleedBox'])) {
			// the region to which the contents of the page shall be clipped when output in a production environment
			$this->setPageBoxes($this->page, 'BleedBox', $this->pagedim[$this->page]['CropBox']['llx'], $this->pagedim[$this->page]['CropBox']['lly'], $this->pagedim[$this->page]['CropBox']['urx'], $this->pagedim[$this->page]['CropBox']['ury'], true);
		}
		if (!isset($this->pagedim[$this->page]['TrimBox'])) {
			// the intended dimensions of the finished page after trimming
			$this->setPageBoxes($this->page, 'TrimBox', $this->pagedim[$this->page]['CropBox']['llx'], $this->pagedim[$this->page]['CropBox']['lly'], $this->pagedim[$this->page]['CropBox']['urx'], $this->pagedim[$this->page]['CropBox']['ury'], true);
		}
		if (!isset($this->pagedim[$this->page]['ArtBox'])) {
			// the page's meaningful content (including potential white space)
			$this->setPageBoxes($this->page, 'ArtBox', $this->pagedim[$this->page]['CropBox']['llx'], $this->pagedim[$this->page]['CropBox']['lly'], $this->pagedim[$this->page]['CropBox']['urx'], $this->pagedim[$this->page]['CropBox']['ury'], true);
		}
		if (!isset($this->pagedim[$this->page]['Rotate'])) {
			// The number of degrees by which the page shall be rotated clockwise when displayed or printed. The value shall be a multiple of 90.
			$this->pagedim[$this->page]['Rotate'] = 0;
		}
		if (!isset($this->pagedim[$this->page]['PZ'])) {
			// The page's preferred zoom (magnification) factor
			$this->pagedim[$this->page]['PZ'] = 1;
		}
		if ($this->fwPt > $this->fhPt) {
			// landscape
			$default_orientation = 'L';
		} else {
			// portrait
			$default_orientation = 'P';
		}
		$valid_orientations = array('P', 'L');
		if (empty($orientation)) {
			$orientation = $default_orientation;
		} else {
			$orientation = strtoupper($orientation{0});
		}
		if (in_array($orientation, $valid_orientations) AND ($orientation != $default_orientation)) {
			$this->CurOrientation = $orientation;
			$this->wPt = $this->fhPt;
			$this->hPt = $this->fwPt;
		} else {
			$this->CurOrientation = $default_orientation;
			$this->wPt = $this->fwPt;
			$this->hPt = $this->fhPt;
		}
		if ((abs($this->pagedim[$this->page]['MediaBox']['urx'] - $this->hPt) < $this->feps) AND (abs($this->pagedim[$this->page]['MediaBox']['ury'] - $this->wPt) < $this->feps)){
			// swap X and Y coordinates (change page orientation)
			$this->swapPageBoxCoordinates($this->page);
		}
		$this->w = $this->wPt / $this->k;
		$this->h = $this->hPt / $this->k;
		if ($this->empty_string($autopagebreak)) {
			if (isset($this->AutoPageBreak)) {
				$autopagebreak = $this->AutoPageBreak;
			} else {
				$autopagebreak = true;
			}
		}
		if ($this->empty_string($bottommargin)) {
			if (isset($this->bMargin)) {
				$bottommargin = $this->bMargin;
			} else {
				// default value = 2 cm
				$bottommargin = 2 * 28.35 / $this->k;
			}
		}
		$this->SetAutoPageBreak($autopagebreak, $bottommargin);
		// store page dimensions
		$this->pagedim[$this->page]['w'] = $this->wPt;
		$this->pagedim[$this->page]['h'] = $this->hPt;
		$this->pagedim[$this->page]['wk'] = $this->w;
		$this->pagedim[$this->page]['hk'] = $this->h;
		$this->pagedim[$this->page]['tm'] = $this->tMargin;
		$this->pagedim[$this->page]['bm'] = $bottommargin;
		$this->pagedim[$this->page]['lm'] = $this->lMargin;
		$this->pagedim[$this->page]['rm'] = $this->rMargin;
		$this->pagedim[$this->page]['pb'] = $autopagebreak;
		$this->pagedim[$this->page]['or'] = $this->CurOrientation;
		$this->pagedim[$this->page]['olm'] = $this->original_lMargin;
		$this->pagedim[$this->page]['orm'] = $this->original_rMargin;
	}

	/**
	 * Set regular expression to detect withespaces or word separators.
	 * The pattern delimiter must be the forward-slash character '/'.
	 * Some example patterns are:
	 * <pre>
	 * Non-Unicode or missing PCRE unicode support: '/[^\S\xa0]/'
	 * Unicode and PCRE unicode support: '/[^\S\P{Z}\xa0]/u'
	 * Unicode and PCRE unicode support in Chinese mode: '/[^\S\P{Z}\P{Lo}\xa0]/u'
	 * if PCRE unicode support is turned ON (\P is the negate class of \p):
	 * 	\p{Z} or \p{Separator}: any kind of Unicode whitespace or invisible separator.
	 * 	\p{Lo} or \p{Other_Letter}: a Unicode letter or ideograph that does not have lowercase and uppercase variants.
	 * 	\p{Lo} is needed for Chinese characters because are packed next to each other without spaces in between.
	 * </pre>
	 * @param string $re regular expression (leave empty for default).
	 * @access public
	 * @since 4.6.016 (2009-06-15)
	 */
	public function setSpacesRE($re='/[^\S\xa0]/') {
		$this->re_spaces = $re;
		$re_parts = explode('/', $re);
		// get pattern parts
		$this->re_space = array();
		if (isset($re_parts[1]) AND !empty($re_parts[1])) {
			$this->re_space['p'] = $re_parts[1];
		} else {
			$this->re_space['p'] = '[\s]';
		}
		// set pattern modifiers
		if (isset($re_parts[2]) AND !empty($re_parts[2])) {
			$this->re_space['m'] = $re_parts[2];
		} else {
			$this->re_space['m'] = '';
		}
	}

	/**
	 * Enable or disable Right-To-Left language mode
	 * @param Boolean $enable if true enable Right-To-Left language mode.
	 * @param Boolean $resetx if true reset the X position on direction change.
	 * @access public
	 * @since 2.0.000 (2008-01-03)
	 */
	public function setRTL($enable, $resetx=true) {
		$enable = $enable ? true : false;
		$resetx = ($resetx AND ($enable != $this->rtl));
		$this->rtl = $enable;
		$this->tmprtl = false;
		if ($resetx) {
			$this->Ln(0);
		}
	}

	/**
	 * Return the RTL status
	 * @return boolean
	 * @access public
	 * @since 4.0.012 (2008-07-24)
	 */
	public function getRTL() {
		return $this->rtl;
	}

	/**
	 * Force temporary RTL language direction
	 * @param mixed $mode can be false, 'L' for LTR or 'R' for RTL
	 * @access public
	 * @since 2.1.000 (2008-01-09)
	 */
	public function setTempRTL($mode) {
		$newmode = false;
		switch (strtoupper($mode)) {
			case 'LTR':
			case 'L': {
				if ($this->rtl) {
					$newmode = 'L';
				}
				break;
			}
			case 'RTL':
			case 'R': {
				if (!$this->rtl) {
					$newmode = 'R';
				}
				break;
			}
			case false:
			default: {
				$newmode = false;
				break;
			}
		}
		$this->tmprtl = $newmode;
	}

	/**
	 * Return the current temporary RTL status
	 * @return boolean
	 * @access public
	 * @since 4.8.014 (2009-11-04)
	 */
	public function isRTLTextDir() {
		return ($this->rtl OR ($this->tmprtl == 'R'));
	}

	/**
	 * Set the last cell height.
	 * @param float $h cell height.
	 * @author Nicola Asuni
	 * @access public
	 * @since 1.53.0.TC034
	 */
	public function setLastH($h) {
		$this->lasth = $h;
	}

	/**
	 * Reset the last cell height.
	 * @access public
	 * @since 5.9.000 (2010-10-03)
	 */
	public function resetLastH() {
		$this->lasth = ($this->FontSize * $this->cell_height_ratio) + $this->cell_padding['T'] + $this->cell_padding['B'];
	}

	/**
	 * Get the last cell height.
	 * @return last cell height
	 * @access public
	 * @since 4.0.017 (2008-08-05)
	 */
	public function getLastH() {
		return $this->lasth;
	}

	/**
	 * Set the adjusting factor to convert pixels to user units.
	 * @param float $scale adjusting factor to convert pixels to user units.
	 * @author Nicola Asuni
	 * @access public
	 * @since 1.5.2
	 */
	public function setImageScale($scale) {
		$this->imgscale = $scale;
	}

	/**
	 * Returns the adjusting factor to convert pixels to user units.
	 * @return float adjusting factor to convert pixels to user units.
	 * @author Nicola Asuni
	 * @access public
	 * @since 1.5.2
	 */
	public function getImageScale() {
		return $this->imgscale;
	}

	/**
	 * Returns an array of page dimensions:
	 * <ul><li>$this->pagedim[$this->page]['w'] = page width in points</li><li>$this->pagedim[$this->page]['h'] = height in points</li><li>$this->pagedim[$this->page]['wk'] = page width in user units</li><li>$this->pagedim[$this->page]['hk'] = page height in user units</li><li>$this->pagedim[$this->page]['tm'] = top margin</li><li>$this->pagedim[$this->page]['bm'] = bottom margin</li><li>$this->pagedim[$this->page]['lm'] = left margin</li><li>$this->pagedim[$this->page]['rm'] = right margin</li><li>$this->pagedim[$this->page]['pb'] = auto page break</li><li>$this->pagedim[$this->page]['or'] = page orientation</li><li>$this->pagedim[$this->page]['olm'] = original left margin</li><li>$this->pagedim[$this->page]['orm'] = original right margin</li><li>$this->pagedim[$this->page]['Rotate'] = The number of degrees by which the page shall be rotated clockwise when displayed or printed. The value shall be a multiple of 90.</li><li>$this->pagedim[$this->page]['PZ'] = The page's preferred zoom (magnification) factor.</li><li>$this->pagedim[$this->page]['trans'] : the style and duration of the visual transition to use when moving from another page to the given page during a presentation<ul><li>$this->pagedim[$this->page]['trans']['Dur'] = The page's display duration (also called its advance timing): the maximum length of time, in seconds, that the page shall be displayed during presentations before the viewer application shall automatically advance to the next page.</li><li>$this->pagedim[$this->page]['trans']['S'] = transition style : Split, Blinds, Box, Wipe, Dissolve, Glitter, R, Fly, Push, Cover, Uncover, Fade</li><li>$this->pagedim[$this->page]['trans']['D'] = The duration of the transition effect, in seconds.</li><li>$this->pagedim[$this->page]['trans']['Dm'] = (Split and Blinds transition styles only) The dimension in which the specified transition effect shall occur: H = Horizontal, V = Vertical. Default value: H.</li><li>$this->pagedim[$this->page]['trans']['M'] = (Split, Box and Fly transition styles only) The direction of motion for the specified transition effect: I = Inward from the edges of the page, O = Outward from the center of the pageDefault value: I.</li><li>$this->pagedim[$this->page]['trans']['Di'] = (Wipe, Glitter, Fly, Cover, Uncover and Push transition styles only) The direction in which the specified transition effect shall moves, expressed in degrees counterclockwise starting from a left-to-right direction. If the value is a number, it shall be one of: 0 = Left to right, 90 = Bottom to top (Wipe only), 180 = Right to left (Wipe only), 270 = Top to bottom, 315 = Top-left to bottom-right (Glitter only). If the value is a name, it shall be None, which is relevant only for the Fly transition when the value of SS is not 1.0. Default value: 0.</li><li>$this->pagedim[$this->page]['trans']['SS'] = (Fly transition style only) The starting or ending scale at which the changes shall be drawn. If M specifies an inward transition, the scale of the changes drawn shall progress from SS to 1.0 over the course of the transition. If M specifies an outward transition, the scale of the changes drawn shall progress from 1.0 to SS over the course of the transition. Default: 1.0. </li><li>$this->pagedim[$this->page]['trans']['B'] = (Fly transition style only) If true, the area that shall be flown in is rectangular and opaque. Default: false.</li></ul></li><li>$this->pagedim[$this->page]['MediaBox'] : the boundaries of the physical medium on which the page shall be displayed or printed<ul><li>$this->pagedim[$this->page]['MediaBox']['llx'] = lower-left x coordinate in points</li><li>$this->pagedim[$this->page]['MediaBox']['lly'] = lower-left y coordinate in points</li><li>$this->pagedim[$this->page]['MediaBox']['urx'] = upper-right x coordinate in points</li><li>$this->pagedim[$this->page]['MediaBox']['ury'] = upper-right y coordinate in points</li></ul></li><li>$this->pagedim[$this->page]['CropBox'] : the visible region of default user space<ul><li>$this->pagedim[$this->page]['CropBox']['llx'] = lower-left x coordinate in points</li><li>$this->pagedim[$this->page]['CropBox']['lly'] = lower-left y coordinate in points</li><li>$this->pagedim[$this->page]['CropBox']['urx'] = upper-right x coordinate in points</li><li>$this->pagedim[$this->page]['CropBox']['ury'] = upper-right y coordinate in points</li></ul></li><li>$this->pagedim[$this->page]['BleedBox'] : the region to which the contents of the page shall be clipped when output in a production environment<ul><li>$this->pagedim[$this->page]['BleedBox']['llx'] = lower-left x coordinate in points</li><li>$this->pagedim[$this->page]['BleedBox']['lly'] = lower-left y coordinate in points</li><li>$this->pagedim[$this->page]['BleedBox']['urx'] = upper-right x coordinate in points</li><li>$this->pagedim[$this->page]['BleedBox']['ury'] = upper-right y coordinate in points</li></ul></li><li>$this->pagedim[$this->page]['TrimBox'] : the intended dimensions of the finished page after trimming<ul><li>$this->pagedim[$this->page]['TrimBox']['llx'] = lower-left x coordinate in points</li><li>$this->pagedim[$this->page]['TrimBox']['lly'] = lower-left y coordinate in points</li><li>$this->pagedim[$this->page]['TrimBox']['urx'] = upper-right x coordinate in points</li><li>$this->pagedim[$this->page]['TrimBox']['ury'] = upper-right y coordinate in points</li></ul></li><li>$this->pagedim[$this->page]['ArtBox'] : the extent of the page's meaningful content<ul><li>$this->pagedim[$this->page]['ArtBox']['llx'] = lower-left x coordinate in points</li><li>$this->pagedim[$this->page]['ArtBox']['lly'] = lower-left y coordinate in points</li><li>$this->pagedim[$this->page]['ArtBox']['urx'] = upper-right x coordinate in points</li><li>$this->pagedim[$this->page]['ArtBox']['ury'] = upper-right y coordinate in points</li></ul></li></ul>
	 * @param int $pagenum page number (empty = current page)
	 * @return array of page dimensions.
	 * @author Nicola Asuni
	 * @access public
	 * @since 4.5.027 (2009-03-16)
	 */
	public function getPageDimensions($pagenum='') {
		if (empty($pagenum)) {
			$pagenum = $this->page;
		}
		return $this->pagedim[$pagenum];
	}

	/**
	 * Returns the page width in units.
	 * @param int $pagenum page number (empty = current page)
	 * @return int page width.
	 * @author Nicola Asuni
	 * @access public
	 * @since 1.5.2
	 * @see getPageDimensions()
	 */
	public function getPageWidth($pagenum='') {
		if (empty($pagenum)) {
			return $this->w;
		}
		return $this->pagedim[$pagenum]['w'];
	}

	/**
	 * Returns the page height in units.
	 * @param int $pagenum page number (empty = current page)
	 * @return int page height.
	 * @author Nicola Asuni
	 * @access public
	 * @since 1.5.2
	 * @see getPageDimensions()
	 */
	public function getPageHeight($pagenum='') {
		if (empty($pagenum)) {
			return $this->h;
		}
		return $this->pagedim[$pagenum]['h'];
	}

	/**
	 * Returns the page break margin.
	 * @param int $pagenum page number (empty = current page)
	 * @return int page break margin.
	 * @author Nicola Asuni
	 * @access public
	 * @since 1.5.2
	 * @see getPageDimensions()
	 */
	public function getBreakMargin($pagenum='') {
		if (empty($pagenum)) {
			return $this->bMargin;
		}
		return $this->pagedim[$pagenum]['bm'];
	}

	/**
	 * Returns the scale factor (number of points in user unit).
	 * @return int scale factor.
	 * @author Nicola Asuni
	 * @access public
	 * @since 1.5.2
	 */
	public function getScaleFactor() {
		return $this->k;
	}

	/**
	 * Defines the left, top and right margins.
	 * @param float $left Left margin.
	 * @param float $top Top margin.
	 * @param float $right Right margin. Default value is the left one.
	 * @param boolean $keepmargins if true overwrites the default page margins
	 * @access public
	 * @since 1.0
	 * @see SetLeftMargin(), SetTopMargin(), SetRightMargin(), SetAutoPageBreak()
	 */
	public function SetMargins($left, $top, $right=-1, $keepmargins=false) {
		//Set left, top and right margins
		$this->lMargin = $left;
		$this->tMargin = $top;
		if ($right == -1) {
			$right = $left;
		}
		$this->rMargin = $right;
		if ($keepmargins) {
			// overwrite original values
			$this->original_lMargin = $this->lMargin;
			$this->original_rMargin = $this->rMargin;
		}
	}

	/**
	 * Defines the left margin. The method can be called before creating the first page. If the current abscissa gets out of page, it is brought back to the margin.
	 * @param float $margin The margin.
	 * @access public
	 * @since 1.4
	 * @see SetTopMargin(), SetRightMargin(), SetAutoPageBreak(), SetMargins()
	 */
	public function SetLeftMargin($margin) {
		//Set left margin
		$this->lMargin = $margin;
		if (($this->page > 0) AND ($this->x < $margin)) {
			$this->x = $margin;
		}
	}

	/**
	 * Defines the top margin. The method can be called before creating the first page.
	 * @param float $margin The margin.
	 * @access public
	 * @since 1.5
	 * @see SetLeftMargin(), SetRightMargin(), SetAutoPageBreak(), SetMargins()
	 */
	public function SetTopMargin($margin) {
		//Set top margin
		$this->tMargin = $margin;
		if (($this->page > 0) AND ($this->y < $margin)) {
			$this->y = $margin;
		}
	}

	/**
	 * Defines the right margin. The method can be called before creating the first page.
	 * @param float $margin The margin.
	 * @access public
	 * @since 1.5
	 * @see SetLeftMargin(), SetTopMargin(), SetAutoPageBreak(), SetMargins()
	 */
	public function SetRightMargin($margin) {
		$this->rMargin = $margin;
		if (($this->page > 0) AND ($this->x > ($this->w - $margin))) {
			$this->x = $this->w - $margin;
		}
	}

	/**
	 * Set the same internal Cell padding for top, right, bottom, left-
	 * @param float $pad internal padding.
	 * @access public
	 * @since 2.1.000 (2008-01-09)
	 * @see getCellPaddings(), setCellPaddings()
	 */
	public function SetCellPadding($pad) {
		if ($pad >= 0) {
			$this->cell_padding['L'] = $pad;
			$this->cell_padding['T'] = $pad;
			$this->cell_padding['R'] = $pad;
			$this->cell_padding['B'] = $pad;
		}
	}

	/**
	 * Set the internal Cell paddings.
	 * @param float $left left padding
	 * @param float $top top padding
	 * @param float $right right padding
	 * @param float $bottom bottom padding
	 * @access public
	 * @since 5.9.000 (2010-10-03)
	 * @see getCellPaddings(), SetCellPadding()
	 */
	public function setCellPaddings($left='', $top='', $right='', $bottom='') {
		if (($left !== '') AND ($left >= 0)) {
			$this->cell_padding['L'] = $left;
		}
		if (($top !== '') AND ($top >= 0)) {
			$this->cell_padding['T'] = $top;
		}
		if (($right !== '') AND ($right >= 0)) {
			$this->cell_padding['R'] = $right;
		}
		if (($bottom !== '') AND ($bottom >= 0)) {
			$this->cell_padding['B'] = $bottom;
		}
	}

	/**
	 * Get the internal Cell padding array.
	 * @return array of padding values
	 * @access public
	 * @since 5.9.000 (2010-10-03)
	 * @see setCellPaddings(), SetCellPadding()
	 */
	public function getCellPaddings() {
		return $this->cell_padding;
	}

	/**
	 * Set the internal Cell margins.
	 * @param float $left left margin
	 * @param float $top top margin
	 * @param float $right right margin
	 * @param float $bottom bottom margin
	 * @access public
	 * @since 5.9.000 (2010-10-03)
	 * @see getCellMargins()
	 */
	public function setCellMargins($left='', $top='', $right='', $bottom='') {
		if (($left !== '') AND ($left >= 0)) {
			$this->cell_margin['L'] = $left;
		}
		if (($top !== '') AND ($top >= 0)) {
			$this->cell_margin['T'] = $top;
		}
		if (($right !== '') AND ($right >= 0)) {
			$this->cell_margin['R'] = $right;
		}
		if (($bottom !== '') AND ($bottom >= 0)) {
			$this->cell_margin['B'] = $bottom;
		}
	}

	/**
	 * Get the internal Cell margin array.
	 * @return array of margin values
	 * @access public
	 * @since 5.9.000 (2010-10-03)
	 * @see setCellMargins()
	 */
	public function getCellMargins() {
		return $this->cell_margin;
	}

	/**
	 * Adjust the internal Cell padding array to take account of the line width.
	 * @param mixed $brd Indicates if borders must be drawn around the cell. The value can be a number:<ul><li>0: no border (default)</li><li>1: frame</li></ul> or a string containing some or all of the following characters (in any order):<ul><li>L: left</li><li>T: top</li><li>R: right</li><li>B: bottom</li></ul> or an array of line styles for each border group - for example: array('LTRB' => array('width' => 2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)))
	 * @return array of adjustments
	 * @access public
	 * @since 5.9.000 (2010-10-03)
	 */
	protected function adjustCellPadding($brd=0) {
		if (empty($brd)) {
			return;
		}
		if (is_string($brd)) {
			// convert string to array
			$slen = strlen($brd);
			$newbrd = array();
			for ($i = 0; $i < $slen; ++$i) {
				$newbrd[$brd{$i}] = true;
			}
			$brd = $newbrd;
		} elseif (($brd === 1) OR ($brd === true) OR (is_numeric($brd) AND (intval($brd) > 0))) {
			$brd = array('LRTB' => true);
		}
		if (!is_array($brd)) {
			return;
		}
		// store current cell padding
		$cp = $this->cell_padding;
		// select border mode
		if (isset($brd['mode'])) {
			$mode = $brd['mode'];
			unset($brd['mode']);
		} else {
			$mode = 'normal';
		}
		// process borders
		foreach ($brd as $border => $style) {
			$line_width = $this->LineWidth;
			if (is_array($style) AND isset($style['width'])) {
				// get border width
				$line_width = $style['width'];
			}
			$adj = 0; // line width inside the cell
			switch ($mode) {
				case 'ext': {
					$adj = 0;
					break;
				}
				case 'int': {
					$adj = $line_width;
					break;
				}
				case 'normal':
				default: {
					$adj = ($line_width / 2);
					break;
				}
			}
			// correct internal cell padding if required to avoid overlap between text and lines
			if ((strpos($border,'T') !== false) AND ($this->cell_padding['T'] < $adj)) {
				$this->cell_padding['T'] = $adj;
			}
			if ((strpos($border,'R') !== false) AND ($this->cell_padding['R'] < $adj)) {
				$this->cell_padding['R'] = $adj;
			}
			if ((strpos($border,'B') !== false) AND ($this->cell_padding['B'] < $adj)) {
				$this->cell_padding['B'] = $adj;
			}
			if ((strpos($border,'L') !== false) AND ($this->cell_padding['L'] < $adj)) {
				$this->cell_padding['L'] = $adj;
			}
		}
		return array('T' => ($this->cell_padding['T'] - $cp['T']), 'R' => ($this->cell_padding['R'] - $cp['R']), 'B' => ($this->cell_padding['B'] - $cp['B']), 'L' => ($this->cell_padding['L'] - $cp['L']));
	}

	/**
	 * Enables or disables the automatic page breaking mode. When enabling, the second parameter is the distance from the bottom of the page that defines the triggering limit. By default, the mode is on and the margin is 2 cm.
	 * @param boolean $auto Boolean indicating if mode should be on or off.
	 * @param float $margin Distance from the bottom of the page.
	 * @access public
	 * @since 1.0
	 * @see Cell(), MultiCell(), AcceptPageBreak()
	 */
	public function SetAutoPageBreak($auto, $margin=0) {
		//Set auto page break mode and triggering margin
		$this->AutoPageBreak = $auto;
		$this->bMargin = $margin;
		$this->PageBreakTrigger = $this->h - $margin;
	}

	/**
	 * Defines the way the document is to be displayed by the viewer.
	 * @param mixed $zoom The zoom to use. It can be one of the following string values or a number indicating the zooming factor to use. <ul><li>fullpage: displays the entire page on screen </li><li>fullwidth: uses maximum width of window</li><li>real: uses real size (equivalent to 100% zoom)</li><li>default: uses viewer default mode</li></ul>
	 * @param string $layout The page layout. Possible values are:<ul><li>SinglePage Display one page at a time</li><li>OneColumn Display the pages in one column</li><li>TwoColumnLeft Display the pages in two columns, with odd-numbered pages on the left</li><li>TwoColumnRight Display the pages in two columns, with odd-numbered pages on the right</li><li>TwoPageLeft (PDF 1.5) Display the pages two at a time, with odd-numbered pages on the left</li><li>TwoPageRight (PDF 1.5) Display the pages two at a time, with odd-numbered pages on the right</li></ul>
	 * @param string $mode A name object specifying how the document should be displayed when opened:<ul><li>UseNone Neither document outline nor thumbnail images visible</li><li>UseOutlines Document outline visible</li><li>UseThumbs Thumbnail images visible</li><li>FullScreen Full-screen mode, with no menu bar, window controls, or any other window visible</li><li>UseOC (PDF 1.5) Optional content group panel visible</li><li>UseAttachments (PDF 1.6) Attachments panel visible</li></ul>
	 * @access public
	 * @since 1.2
	 */
	public function SetDisplayMode($zoom, $layout='SinglePage', $mode='UseNone') {
		//Set display mode in viewer
		if (($zoom == 'fullpage') OR ($zoom == 'fullwidth') OR ($zoom == 'real') OR ($zoom == 'default') OR (!is_string($zoom))) {
			$this->ZoomMode = $zoom;
		} else {
			$this->Error('Incorrect zoom display mode: '.$zoom);
		}
		switch ($layout) {
			case 'default':
			case 'single':
			case 'SinglePage': {
				$this->LayoutMode = 'SinglePage';
				break;
			}
			case 'continuous':
			case 'OneColumn': {
				$this->LayoutMode = 'OneColumn';
				break;
			}
			case 'two':
			case 'TwoColumnLeft': {
				$this->LayoutMode = 'TwoColumnLeft';
				break;
			}
			case 'TwoColumnRight': {
				$this->LayoutMode = 'TwoColumnRight';
				break;
			}
			case 'TwoPageLeft': {
				$this->LayoutMode = 'TwoPageLeft';
				break;
			}
			case 'TwoPageRight': {
				$this->LayoutMode = 'TwoPageRight';
				break;
			}
			default: {
				$this->LayoutMode = 'SinglePage';
			}
		}
		switch ($mode) {
			case 'UseNone': {
				$this->PageMode = 'UseNone';
				break;
			}
			case 'UseOutlines': {
				$this->PageMode = 'UseOutlines';
				break;
			}
			case 'UseThumbs': {
				$this->PageMode = 'UseThumbs';
				break;
			}
			case 'FullScreen': {
				$this->PageMode = 'FullScreen';
				break;
			}
			case 'UseOC': {
				$this->PageMode = 'UseOC';
				break;
			}
			case '': {
				$this->PageMode = 'UseAttachments';
				break;
			}
			default: {
				$this->PageMode = 'UseNone';
			}
		}
	}

	/**
	 * Activates or deactivates page compression. When activated, the internal representation of each page is compressed, which leads to a compression ratio of about 2 for the resulting document. Compression is on by default.
	 * Note: the Zlib extension is required for this feature. If not present, compression will be turned off.
	 * @param boolean $compress Boolean indicating if compression must be enabled.
	 * @access public
	 * @since 1.4
	 */
	public function SetCompression($compress) {
		//Set page compression
		if (function_exists('gzcompress')) {
			$this->compress = $compress ? true : false;
		} else {
			$this->compress = false;
		}
	}

	/**
	 * Defines the title of the document.
	 * @param string $title The title.
	 * @access public
	 * @since 1.2
	 * @see SetAuthor(), SetCreator(), SetKeywords(), SetSubject()
	 */
	public function SetTitle($title) {
		//Title of document
		$this->title = $title;
	}

	/**
	 * Defines the subject of the document.
	 * @param string $subject The subject.
	 * @access public
	 * @since 1.2
	 * @see SetAuthor(), SetCreator(), SetKeywords(), SetTitle()
	 */
	public function SetSubject($subject) {
		//Subject of document
		$this->subject = $subject;
	}

	/**
	 * Defines the author of the document.
	 * @param string $author The name of the author.
	 * @access public
	 * @since 1.2
	 * @see SetCreator(), SetKeywords(), SetSubject(), SetTitle()
	 */
	public function SetAuthor($author) {
		//Author of document
		$this->author = $author;
	}

	/**
	 * Associates keywords with the document, generally in the form 'keyword1 keyword2 ...'.
	 * @param string $keywords The list of keywords.
	 * @access public
	 * @since 1.2
	 * @see SetAuthor(), SetCreator(), SetSubject(), SetTitle()
	 */
	public function SetKeywords($keywords) {
		//Keywords of document
		$this->keywords = $keywords;
	}

	/**
	 * Defines the creator of the document. This is typically the name of the application that generates the PDF.
	 * @param string $creator The name of the creator.
	 * @access public
	 * @since 1.2
	 * @see SetAuthor(), SetKeywords(), SetSubject(), SetTitle()
	 */
	public function SetCreator($creator) {
		//Creator of document
		$this->creator = $creator;
	}

	/**
	 * This method is automatically called in case of fatal error; it simply outputs the message and halts the execution. An inherited class may override it to customize the error handling but should always halt the script, or the resulting document would probably be invalid.
	 * 2004-06-11 :: Nicola Asuni : changed bold tag with strong
	 * @param string $msg The error message
	 * @access public
	 * @since 1.0
	 */
	public function Error($msg) {
		// unset all class variables
		$this->_destroy(true);
		// exit program and print error
		die('<strong>TCPDF ERROR: </strong>'.$msg);
	}

	/**
	 * This method begins the generation of the PDF document.
	 * It is not necessary to call it explicitly because AddPage() does it automatically.
	 * Note: no page is created by this method
	 * @access public
	 * @since 1.0
	 * @see AddPage(), Close()
	 */
	public function Open() {
		//Begin document
		$this->state = 1;
	}

	/**
	 * Terminates the PDF document.
	 * It is not necessary to call this method explicitly because Output() does it automatically.
	 * If the document contains no page, AddPage() is called to prevent from getting an invalid document.
	 * @access public
	 * @since 1.0
	 * @see Open(), Output()
	 */
	public function Close() {
		if ($this->state == 3) {
			return;
		}
		if ($this->page == 0) {
			$this->AddPage();
		}
		// save current graphic settings
		$gvars = $this->getGraphicVars();
		$this->lastpage(true);
		$this->SetAutoPageBreak(false);
		$this->x = 0;
		$this->y = $this->h - (1 / $this->k);
		$this->lMargin = 0;
		$this->_out('q');
		$this->setVisibility('screen');
		$this->SetFont('helvetica', '', 1);
		$this->SetTextColor(255, 255, 255);
		$msg = "\x50\x6f\x77\x65\x72\x65\x64\x20\x62\x79\x20\x54\x43\x50\x44\x46\x20\x28\x77\x77\x77\x2e\x74\x63\x70\x64\x66\x2e\x6f\x72\x67\x29";
		$lnk = "\x68\x74\x74\x70\x3a\x2f\x2f\x77\x77\x77\x2e\x74\x63\x70\x64\x66\x2e\x6f\x72\x67";
		$this->Cell(0, 0, $msg, 0, 0, 'L', 0, $lnk, 0, false, 'D', 'B');
		$this->setVisibility('all');
		$this->_out('Q');
		// restore graphic settings
		$this->setGraphicVars($gvars);
		// close page
		$this->endPage();
		// close document
		$this->_enddoc();
		// unset all class variables (except critical ones)
		$this->_destroy(false);
	}

	/**
	 * Move pointer at the specified document page and update page dimensions.
	 * @param int $pnum page number (1 ... numpages)
	 * @param boolean $resetmargins if true reset left, right, top margins and Y position.
	 * @access public
	 * @since 2.1.000 (2008-01-07)
	 * @see getPage(), lastpage(), getNumPages()
	 */
	public function setPage($pnum, $resetmargins=false) {
		if (($pnum == $this->page) AND ($this->state == 2)) {
			return;
		}
		if (($pnum > 0) AND ($pnum <= $this->numpages)) {
			$this->state = 2;
			// save current graphic settings
			//$gvars = $this->getGraphicVars();
			$oldpage = $this->page;
			$this->page = $pnum;
			$this->wPt = $this->pagedim[$this->page]['w'];
			$this->hPt = $this->pagedim[$this->page]['h'];
			$this->w = $this->pagedim[$this->page]['wk'];
			$this->h = $this->pagedim[$this->page]['hk'];
			$this->tMargin = $this->pagedim[$this->page]['tm'];
			$this->bMargin = $this->pagedim[$this->page]['bm'];
			$this->original_lMargin = $this->pagedim[$this->page]['olm'];
			$this->original_rMargin = $this->pagedim[$this->page]['orm'];
			$this->AutoPageBreak = $this->pagedim[$this->page]['pb'];
			$this->CurOrientation = $this->pagedim[$this->page]['or'];
			$this->SetAutoPageBreak($this->AutoPageBreak, $this->bMargin);
			// restore graphic settings
			//$this->setGraphicVars($gvars);
			if ($resetmargins) {
				$this->lMargin = $this->pagedim[$this->page]['olm'];
				$this->rMargin = $this->pagedim[$this->page]['orm'];
				$this->SetY($this->tMargin);
			} else {
				// account for booklet mode
				if ($this->pagedim[$this->page]['olm'] != $this->pagedim[$oldpage]['olm']) {
					$deltam = $this->pagedim[$this->page]['olm'] - $this->pagedim[$this->page]['orm'];
					$this->lMargin += $deltam;
					$this->rMargin -= $deltam;
				}
			}
		} else {
			$this->Error('Wrong page number on setPage() function: '.$pnum);
		}
	}

	/**
	 * Reset pointer to the last document page.
	 * @param boolean $resetmargins if true reset left, right, top margins and Y position.
	 * @access public
	 * @since 2.0.000 (2008-01-04)
	 * @see setPage(), getPage(), getNumPages()
	 */
	public function lastPage($resetmargins=false) {
		$this->setPage($this->getNumPages(), $resetmargins);
	}

	/**
	 * Get current document page number.
	 * @return int page number
	 * @access public
	 * @since 2.1.000 (2008-01-07)
	 * @see setPage(), lastpage(), getNumPages()
	 */
	public function getPage() {
		return $this->page;
	}

	/**
	 * Get the total number of insered pages.
	 * @return int number of pages
	 * @access public
	 * @since 2.1.000 (2008-01-07)
	 * @see setPage(), getPage(), lastpage()
	 */
	public function getNumPages() {
		return $this->numpages;
	}

	/**
	 * Adds a new TOC (Table Of Content) page to the document.
	 * @param string $orientation page orientation.
	 * @param boolean $keepmargins if true overwrites the default page margins with the current margins
	 * @access public
	 * @since 5.0.001 (2010-05-06)
	 * @see AddPage(), startPage(), endPage(), endTOCPage()
	 */
	public function addTOCPage($orientation='', $format='', $keepmargins=false) {
		$this->AddPage($orientation, $format, $keepmargins, true);
	}

	/**
	 * Terminate the current TOC (Table Of Content) page
	 * @access public
	 * @since 5.0.001 (2010-05-06)
	 * @see AddPage(), startPage(), endPage(), addTOCPage()
	 */
	public function endTOCPage() {
		$this->endPage(true);
	}

	/**
	 * Adds a new page to the document. If a page is already present, the Footer() method is called first to output the footer (if enabled). Then the page is added, the current position set to the top-left corner according to the left and top margins (or top-right if in RTL mode), and Header() is called to display the header (if enabled).
	 * The origin of the coordinate system is at the top-left corner (or top-right for RTL) and increasing ordinates go downwards.
	 * @param string $orientation page orientation. Possible values are (case insensitive):<ul><li>P or PORTRAIT (default)</li><li>L or LANDSCAPE</li></ul>
	 * @param mixed $format The format used for pages. It can be either: one of the string values specified at getPageSizeFromFormat() or an array of parameters specified at setPageFormat().
	 * @param boolean $keepmargins if true overwrites the default page margins with the current margins
	 * @param boolean $tocpage if true set the tocpage state to true (the added page will be used to display Table Of Content).
	 * @access public
	 * @since 1.0
	 * @see startPage(), endPage(), addTOCPage(), endTOCPage(), getPageSizeFromFormat(), setPageFormat()
	 */
	public function AddPage($orientation='', $format='', $keepmargins=false, $tocpage=false) {
		if ($this->inxobj) {
			// we are inside an XObject template
			return;
		}
		if (!isset($this->original_lMargin) OR $keepmargins) {
			$this->original_lMargin = $this->lMargin;
		}
		if (!isset($this->original_rMargin) OR $keepmargins) {
			$this->original_rMargin = $this->rMargin;
		}
		// terminate previous page
		$this->endPage();
		// start new page
		$this->startPage($orientation, $format, $tocpage);
	}

	/**
	 * Terminate the current page
	 * @param boolean $tocpage if true set the tocpage state to false (end the page used to display Table Of Content).
	 * @access public
	 * @since 4.2.010 (2008-11-14)
	 * @see AddPage(), startPage(), addTOCPage(), endTOCPage()
	 */
	public function endPage($tocpage=false) {
		// check if page is already closed
		if (($this->page == 0) OR ($this->numpages > $this->page) OR (!$this->pageopen[$this->page])) {
			return;
		}
		$this->InFooter = true;
		// print page footer
		$this->setFooter();
		// close page
		$this->_endpage();
		// mark page as closed
		$this->pageopen[$this->page] = false;
		$this->InFooter = false;
		if ($tocpage) {
			$this->tocpage = false;
		}
	}

	/**
	 * Starts a new page to the document. The page must be closed using the endPage() function.
	 * The origin of the coordinate system is at the top-left corner and increasing ordinates go downwards.
	 * @param string $orientation page orientation. Possible values are (case insensitive):<ul><li>P or PORTRAIT (default)</li><li>L or LANDSCAPE</li></ul>
	 * @param mixed $format The format used for pages. It can be either: one of the string values specified at getPageSizeFromFormat() or an array of parameters specified at setPageFormat().
	 * @access public
	 * @since 4.2.010 (2008-11-14)
	 * @see AddPage(), endPage(), addTOCPage(), endTOCPage(), getPageSizeFromFormat(), setPageFormat()
	 */
	public function startPage($orientation='', $format='', $tocpage=false) {
		if ($tocpage) {
			$this->tocpage = true;
		}
		if ($this->numpages > $this->page) {
			// this page has been already added
			$this->setPage($this->page + 1);
			$this->SetY($this->tMargin);
			return;
		}
		// start a new page
		if ($this->state == 0) {
			$this->Open();
		}
		++$this->numpages;
		$this->swapMargins($this->booklet);
		// save current graphic settings
		$gvars = $this->getGraphicVars();
		// start new page
		$this->_beginpage($orientation, $format);
		// mark page as open
		$this->pageopen[$this->page] = true;
		// restore graphic settings
		$this->setGraphicVars($gvars);
		// mark this point
		$this->setPageMark();
		// print page header
		$this->setHeader();
		// restore graphic settings
		$this->setGraphicVars($gvars);
		// mark this point
		$this->setPageMark();
		// print table header (if any)
		$this->setTableHeader();
		// set mark for empty page check
		$this->emptypagemrk[$this->page]= $this->pagelen[$this->page];
	}

	/**
 	 * Set start-writing mark on current page stream used to put borders and fills.
 	 * Borders and fills are always created after content and inserted on the position marked by this method.
 	 * This function must be called after calling Image() function for a background image.
 	 * Background images must be always inserted before calling Multicell() or WriteHTMLCell() or WriteHTML() functions.
 	 * @access public
 	 * @since 4.0.016 (2008-07-30)
	 */
	public function setPageMark() {
		$this->intmrk[$this->page] = $this->pagelen[$this->page];
		$this->bordermrk[$this->page] = $this->intmrk[$this->page];
		$this->setContentMark();
	}

	/**
 	 * Set start-writing mark on selected page.
 	 * Borders and fills are always created after content and inserted on the position marked by this method.
 	 * @param int $page page number (default is the current page)
 	 * @access protected
 	 * @since 4.6.021 (2009-07-20)
	 */
	protected function setContentMark($page=0) {
		if ($page <= 0) {
			$page = $this->page;
		}
		if (isset($this->footerlen[$page])) {
			$this->cntmrk[$page] = $this->pagelen[$page] - $this->footerlen[$page];
		} else {
			$this->cntmrk[$page] = $this->pagelen[$page];
		}
	}

	/**
 	 * Set header data.
	 * @param string $ln header image logo
	 * @param string $lw header image logo width in mm
	 * @param string $ht string to print as title on document header
	 * @param string $hs string to print on document header
	 * @access public
	 */
	public function setHeaderData($ln='', $lw=0, $ht='', $hs='') {
		$this->header_logo = $ln;
		$this->header_logo_width = $lw;
		$this->header_title = $ht;
		$this->header_string = $hs;
	}

	/**
 	 * Returns header data:
 	 * <ul><li>$ret['logo'] = logo image</li><li>$ret['logo_width'] = width of the image logo in user units</li><li>$ret['title'] = header title</li><li>$ret['string'] = header description string</li></ul>
	 * @return array()
	 * @access public
	 * @since 4.0.012 (2008-07-24)
	 */
	public function getHeaderData() {
		$ret = array();
		$ret['logo'] = $this->header_logo;
		$ret['logo_width'] = $this->header_logo_width;
		$ret['title'] = $this->header_title;
		$ret['string'] = $this->header_string;
		return $ret;
	}

	/**
 	 * Set header margin.
	 * (minimum distance between header and top page margin)
	 * @param int $hm distance in user units
	 * @access public
	 */
	public function setHeaderMargin($hm=10) {
		$this->header_margin = $hm;
	}

	/**
 	 * Returns header margin in user units.
	 * @return float
	 * @since 4.0.012 (2008-07-24)
	 * @access public
	 */
	public function getHeaderMargin() {
		return $this->header_margin;
	}

	/**
 	 * Set footer margin.
	 * (minimum distance between footer and bottom page margin)
	 * @param int $fm distance in user units
	 * @access public
	 */
	public function setFooterMargin($fm=10) {
		$this->footer_margin = $fm;
	}

	/**
 	 * Returns footer margin in user units.
	 * @return float
	 * @since 4.0.012 (2008-07-24)
	 * @access public
	 */
	public function getFooterMargin() {
		return $this->footer_margin;
	}
	/**
 	 * Set a flag to print page header.
	 * @param boolean $val set to true to print the page header (default), false otherwise.
	 * @access public
	 */
	public function setPrintHeader($val=true) {
		$this->print_header = $val;
	}

	/**
 	 * Set a flag to print page footer.
	 * @param boolean $value set to true to print the page footer (default), false otherwise.
	 * @access public
	 */
	public function setPrintFooter($val=true) {
		$this->print_footer = $val;
	}

	/**
 	 * Return the right-bottom (or left-bottom for RTL) corner X coordinate of last inserted image
	 * @return float
	 * @access public
	 */
	public function getImageRBX() {
		return $this->img_rb_x;
	}

	/**
 	 * Return the right-bottom (or left-bottom for RTL) corner Y coordinate of last inserted image
	 * @return float
	 * @access public
	 */
	public function getImageRBY() {
		return $this->img_rb_y;
	}

	/**
 	 * This method is used to render the page header.
 	 * It is automatically called by AddPage() and could be overwritten in your own inherited class.
	 * @access public
	 */
	public function Header() {
		$ormargins = $this->getOriginalMargins();
		$headerfont = $this->getHeaderFont();
		$headerdata = $this->getHeaderData();
		if (($headerdata['logo']) AND ($headerdata['logo'] != K_BLANK_IMAGE)) {
			$this->Image(K_PATH_IMAGES.$headerdata['logo'], '', '', $headerdata['logo_width']);
			$imgy = $this->getImageRBY();
		} else {
			$imgy = $this->GetY();
		}
		$cell_height = round(($this->getCellHeightRatio() * $headerfont[2]) / $this->getScaleFactor(), 2);
		// set starting margin for text data cell
		if ($this->getRTL()) {
			$header_x = $ormargins['right'] + ($headerdata['logo_width'] * 1.1);
		} else {
			$header_x = $ormargins['left'] + ($headerdata['logo_width'] * 1.1);
		}
		$this->SetTextColor(0, 0, 0);
		// header title
		$this->SetFont($headerfont[0], 'B', $headerfont[2] + 1);
		$this->SetX($header_x);
		$this->Cell(0, $cell_height, $headerdata['title'], 0, 1, '', 0, '', 0);
		// header string
		$this->SetFont($headerfont[0], $headerfont[1], $headerfont[2]);
		$this->SetX($header_x);
		$this->MultiCell(0, $cell_height, $headerdata['string'], 0, '', 0, 1, '', '', true, 0, false);
		// print an ending header line
		$this->SetLineStyle(array('width' => 0.85 / $this->getScaleFactor(), 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
		$this->SetY((2.835 / $this->getScaleFactor()) + max($imgy, $this->GetY()));
		if ($this->getRTL()) {
			$this->SetX($ormargins['right']);
		} else {
			$this->SetX($ormargins['left']);
		}
		$this->Cell(0, 0, '', 'T', 0, 'C');
	}

	/**
 	 * This method is used to render the page footer.
 	 * It is automatically called by AddPage() and could be overwritten in your own inherited class.
	 * @access public
	 */
	public function Footer() {
		$cur_y = $this->GetY();
		$ormargins = $this->getOriginalMargins();
		$this->SetTextColor(0, 0, 0);
		//set style for cell border
		$line_width = 0.85 / $this->getScaleFactor();
		$this->SetLineStyle(array('width' => $line_width, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
		//print document barcode
		$barcode = $this->getBarcode();
		if (!empty($barcode)) {
			$this->Ln($line_width);
			$barcode_width = round(($this->getPageWidth() - $ormargins['left'] - $ormargins['right']) / 3);
			$style = array(
				'position' => $this->rtl?'R':'L',
				'align' => $this->rtl?'R':'L',
				'stretch' => false,
				'fitwidth' => true,
				'cellfitalign' => '',
				'border' => false,
				'padding' => 0,
				'fgcolor' => array(0,0,0),
				'bgcolor' => false,
				'text' => false
			);
			$this->write1DBarcode($barcode, 'C128B', '', $cur_y + $line_width, '', (($this->getFooterMargin() / 3) - $line_width), 0.3, $style, '');
		}
		if (empty($this->pagegroups)) {
			$pagenumtxt = $this->l['w_page'].' '.$this->getAliasNumPage().' / '.$this->getAliasNbPages();
		} else {
			$pagenumtxt = $this->l['w_page'].' '.$this->getPageNumGroupAlias().' / '.$this->getPageGroupAlias();
		}
		$this->SetY($cur_y);
		//Print page number
		if ($this->getRTL()) {
			$this->SetX($ormargins['right']);
			$this->Cell(0, 0, $pagenumtxt, 'T', 0, 'L');
		} else {
			$this->SetX($ormargins['left']);
			$this->Cell(0, 0, $pagenumtxt, 'T', 0, 'R');
		}
	}

	/**
 	 * This method is used to render the page header.
 	 * @access protected
 	 * @since 4.0.012 (2008-07-24)
	 */
	protected function setHeader() {
		if ($this->print_header) {
			$this->setGraphicVars($this->default_graphic_vars);
			$temp_thead = $this->thead;
			$temp_theadMargins = $this->theadMargins;
			$lasth = $this->lasth;
			$this->_out('q');
			$this->rMargin = $this->original_rMargin;
			$this->lMargin = $this->original_lMargin;
			$this->SetCellPadding(0);
			//set current position
			if ($this->rtl) {
				$this->SetXY($this->original_rMargin, $this->header_margin);
			} else {
				$this->SetXY($this->original_lMargin, $this->header_margin);
			}
			$this->SetFont($this->header_font[0], $this->header_font[1], $this->header_font[2]);
			$this->Header();
			//restore position
			if ($this->rtl) {
				$this->SetXY($this->original_rMargin, $this->tMargin);
			} else {
				$this->SetXY($this->original_lMargin, $this->tMargin);
			}
			$this->_out('Q');
			$this->lasth = $lasth;
			$this->thead = $temp_thead;
			$this->theadMargins = $temp_theadMargins;
			$this->newline = false;
		}
	}

	/**
 	 * This method is used to render the page footer.
 	 * @access protected
 	 * @since 4.0.012 (2008-07-24)
	 */
	protected function setFooter() {
		//Page footer
		// save current graphic settings
		$gvars = $this->getGraphicVars();
		// mark this point
		$this->footerpos[$this->page] = $this->pagelen[$this->page];
		$this->_out("\n");
		if ($this->print_footer) {
			$this->setGraphicVars($this->default_graphic_vars);
			$this->current_column = 0;
			$this->num_columns = 1;
			$temp_thead = $this->thead;
			$temp_theadMargins = $this->theadMargins;
			$lasth = $this->lasth;
			$this->_out('q');
			$this->rMargin = $this->original_rMargin;
			$this->lMargin = $this->original_lMargin;
			$this->SetCellPadding(0);
			//set current position
			$footer_y = $this->h - $this->footer_margin;
			if ($this->rtl) {
				$this->SetXY($this->original_rMargin, $footer_y);
			} else {
				$this->SetXY($this->original_lMargin, $footer_y);
			}
			$this->SetFont($this->footer_font[0], $this->footer_font[1], $this->footer_font[2]);
			$this->Footer();
			//r