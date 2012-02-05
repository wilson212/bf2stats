<?php 

/*
    Copyright (C) 2006  BF2Statistics

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/************************************
 * 11/13/05 v0.0.1 - BETA build     *
 * 11/22/05 v0.0.2 - Updated for SF *
 * 11/28/05 v0.0.3 - Removed strlen *
 * 01/03/06 v0.1 - BETA release     *
 * 02/14/06 v0.1.1 - Updated for EF *
 *                   Changed format *
 * 02/05/06 v0.1.2 - Fixed typo     *
 * 02/04/12 v1.0.0 - Release        *
 ************************************/

//Disable Zlib Compression
ini_set('zlib.output_compression', '0');
 
$out = "O\n" .
	   "H\tver\tnow\n" .
	   "D\t0.1\t" . time() . "\n" .
	   "H\tid\tkit\tname\tdescr\n" .
	   "D\t11\t0\tChsht_protecta\tProtecta shotgun with slugs\n" .
	   "D\t22\t1\tUsrif_g3a3\tH&K G3\n" .
	   "D\t33\t2\tUSSHT_Jackhammer\tJackhammer shotgun\n" .
	   "D\t44\t3\tUsrif_sa80\tSA-80\n" .
	   "D\t55\t4\tUsrif_g36c\tG36C\n" .
	   "D\t66\t5\tRULMG_PKM\tPKM\n" .
	   "D\t77\t6\tUSSNI_M95_Barret\tBarret M82A2 (.50 cal rifle)\n" .
	   "D\t88\t1\tsasrif_fn2000\tFN2000\n" .
	   "D\t99\t2\tsasrif_mp7\tMP-7\n" .
	   "D\t111\t3\tsasrif_g36e\tG36E\n" .
	   "D\t222\t4\tusrif_fnscarl\tFN SCAR - L\n" .
	   "D\t333\t5\tsasrif_mg36\tMG36\n" .
	   "D\t444\t0\teurif_fnp90\tP90\n" .
	   "D\t555\t6\tgbrif_l96a1\tL96A1\n";

$num = strlen(preg_replace('/[\t\n]/','',$out));
print $out . "$\t" . $num . "\t$";
?>