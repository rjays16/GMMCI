<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Billing</title>
<style type="text/css">
<!--
.style1 {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 14px;
	font-weight: bold;
}
.style6 {font-family: Arial, Helvetica, sans-serif; font-size: 12px; }
.style9 {font-family: Arial, Helvetica, sans-serif; font-size: 14px; font-weight: bold; color: #4A5764; }
.style10 {
	color: #FF0000;
	font-size: 12px;
}
.style12 {font-family: Arial, Helvetica, sans-serif; font-size: 14px; font-weight: bold; color: #FFFFFF; }
.style13 {color: #FFFFFF}
.style15 {font-size: 12}
.style17 {color: #FFFFFF; font-size: 12; }
-->
</style>
</head>

<body>
<form id="billing" name="billing" method="post" >
  <table width="100%" border="0" cellspacing="2" cellpadding="0">
    <tr>
      <td valign="top">&nbsp;</td>
      <td><img src="img/billing_banner.jpg"></td>
    </tr>
    <tr>
      <td width="8%" valign="top">&nbsp;</td>
      <td width="92%">
        <table width="90%" border="0" align="center" cellpadding="0" cellspacing="2">
          <tr>
            <td>
              <table width="60%" border="0" cellspacing="1" cellpadding="0">
                <tr>
                  <td bgcolor="#919FB0"><span class="style12">&nbsp;&nbsp;&nbsp;&nbsp;Accomodation</span></td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td>
              <table width="60%" border="0" cellspacing="1" cellpadding="0">
                <tr>
                  <td bgcolor="#B6C0CB">
                    <table width="100%" border="0" cellspacing="1" cellpadding="1">
                      <tr>
                        <td valign="top" bgcolor="#FFFFFF">
                          <table width="100%" border="0" align="center" cellpadding="2" cellspacing="2">
                            <tr>
                              <td width="37%" bgcolor="#FFFFFF"><span class="style6">Admission Date </span></td>
                              <td width="63%" bgcolor="#FFFFFF" class="style6">
                                <?= $admission_dtetme ?>
                              </td>
                            </tr>
                            <tr>
                              <td bgcolor="#FFFFFF"><span class="style6">Discharged</span></td>
                              <td bgcolor="#FFFFFF" class="style6">
                                <input type="text" name="discharge_dtetme" id="discharge_dtetme" value="<?= $discharge_dtetme ?>" onblur="trimString(this)" />
                              </td>
                            </tr>
                            <tr>
                              <td bgcolor="#FFFFFF"><span class="style6">Room Number </span></td>
                              <td bgcolor="#FFFFFF" class="style6">
                                <input type="text" name="room_nr" id="room_nr" value="<?= $room_nr ?>" onblur="trimString(this)" />
                              </td>
                            </tr>
                            <tr>
                              <td bgcolor="#FFFFFF"><span class="style6">Type No. </span></td>
                              <td bgcolor="#FFFFFF" class="style6">
                                <input type="text" name="type_nr" id="room_nr2" value="<?= $type_nr ?>" />
                              </td>
                            </tr>
                            <tr>
                              <td bgcolor="#FFFFFF"><span class="style6">Description</span></td>
                              <td bgcolor="#FFFFFF" class="style6">
                                <input type="text" name="type_desc" id="type_desc" value="<?= $type_desc ?>" />
                              </td>
                            </tr>
                            <tr>
                              <td bgcolor="#FFFFFF"><span class="style6">Rate</span></td>
                              <td bgcolor="#FFFFFF" class="style6">
                                <input type="text" name="rate" id="rate" value="<?= $rate ?>" />
                              </td>
                            </tr>
                            <tr>
                              <td bgcolor="#FFFFFF"><span class="style6">Available Healthcare (No. of Days) </span></td>
                              <td bgcolor="#FFFFFF" class="style6">
                                <input type="text" name="availablehcares" id="availablehcares" value="<?= $availablehcares ?>" />
                              </td>
                            </tr>
                          </table>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
              <center>
              </center>
            </td>
          </tr>
        </table><br />
        <table width="90%" border="0" align="center" cellpadding="0" cellspacing="2">
          <tr>
            <td class="style1">
              <table width="60%" border="0" cellspacing="1" cellpadding="0">
                <tr>
                  <td bgcolor="#919FB0"><span class="style9">&nbsp;<span class="style17">&nbsp;&nbsp;&nbsp;Medicines/Supplies </span></span></td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td>
              <table width="60%" border="0" cellspacing="1" cellpadding="0">
                <tr>
                  <td bgcolor="#B6C0CB">
                    <table width="100%" border="0" cellspacing="1" cellpadding="0">
                      <tr>
                        <td bgcolor="#FFFFFF">
                          <table width="100%" border="0" align="center" cellpadding="0" cellspacing="2">
                            <tr>
                              <td width="37%" class="style6">Reference No. </td>
                              <td width="63%" class="style6">
                                <input type="text" name="ref_no" id="ref_no" value="<?= $ref_no ?>"  />
                              </td>
                            </tr>
                            <tr>
                              <td class="style6">Transaction Date </td>
                              <td class="style6">
                                <input type="text" name="trans_dtetme" id="trans_dtetme" value="<?= $trans_dtetme ?>"  />
                              </td>
                            </tr>
                            <tr>
                              <td class="style6">Product No. </td>
                              <td class="style6">
                                <input type="text" name="bestellnum" id="bestellnum" value="<?= $bestellnum ?>"  />
                              </td>
                            </tr>
                            <tr>
                              <td class="style6">Product Description </td>
                              <td class="style6">
                                <input type="text" name="artikelname" id="artikelname" value="<?= $artikelname ?>"  />
                              </td>
                            </tr>
                            <tr>
                              <td class="style6">Quantity</td>
                              <td class="style6">
                                <input type="text" name="item_qty" id="item_qty" value="<?= $item_qty ?>"  />
                              </td>
                            </tr>
                            <tr>
                              <td class="style6">Price</td>
                              <td class="style6">
                                <input type="text" name="item_price" id="item_price" value="<?= $item_price ?>"  />
                              </td>
                            </tr>
                            <tr>
                              <td class="style6">Discount</td>
                              <td class="style6">
                                <input type="text" name="item_discount" id="item_discount" value="<?= $item_discount ?>"  />
                              </td>
                            </tr>
                          </table>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
		<br />
        <table width="90%" border="0" align="center" cellpadding="0" cellspacing="2">
          <tr>
            <td class="style1">
              <table width="60%" border="0" cellspacing="1" cellpadding="0">
                <tr>
                  <td bgcolor="#919FB0"><span class="style12">&nbsp;&nbsp;&nbsp;<span class="style15">&nbsp;Hospital Services </span></span></td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td valign="top">
              <table width="60%" border="0" cellspacing="1" cellpadding="0">
                <tr>
                  <td bgcolor="#B6C0CB">
                    <table width="100%" border="0" cellspacing="1" cellpadding="0">
                      <tr>
                        <td bgcolor="#FFFFFF">
                          <table width="100%" border="0" align="center" cellpadding="0" cellspacing="2">
                            <tr>
                              <td width="37%" class="style6">Service Code </td>
                              <td width="63%" class="style6">
                                <input type="text" name="service_code" id="service_code" value="<?= $service_code ?>" />
                              </td>
                            </tr>
                            <tr>
                              <td class="style6">Service Description </td>
                              <td class="style6">
                                <input type="text" name="service_desc" id="service_desc" value="<?= $service_desc ?>" />
                              </td>
                            </tr>
                            <tr>
                              <td class="style6">Service Quantity </td>
                              <td class="style6">
                                <input type="text" name="service_qty" id="service_qty" value="<?= $service_qty?>" />
                              </td>
                            </tr>
                            <tr>
                              <td class="style6">Service Price</td>
                              <td class="style6">
                                <input type="text" name="service_price" id="service_price" value="<?= $service_price ?>" />
                              </td>
                            </tr>
                            <tr>
                              <td class="style6">Service Discount</td>
                              <td class="style6">
                                <input type="text" name="service_discount" id="service_discount" value="<?= $service_discount ?>" />
                              </td>
                            </tr>
                          </table>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
		<br />
        <table width="90%" border="0" align="center" cellpadding="0" cellspacing="2">
          <tr>
            <td class="style1">
              <table width="60%" border="0" cellspacing="1" cellpadding="0">
                <tr>
                  <td bgcolor="#919FB0"><span class="style9">&nbsp;<span class="style13">&nbsp;&nbsp;&nbsp;Doctors' Fees </span></span></td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td height="354" valign="top">
              <table width="60%" border="0" cellspacing="1" cellpadding="0">
                <tr>
                  <td bgcolor="#B6C0CB">
                    <table width="100%" border="0" cellspacing="1" cellpadding="0">
                      <tr>
                        <td bgcolor="#FFFFFF">
                          <table width="100%" border="0" align="center" cellpadding="0" cellspacing="2">
                            <tr>
                              <td width="37%" class="style6">Lastname</td>
                              <td width="63%" class="style6">
                                <input type="text" name="doc_last" id="doc_last" value="<?= $doc_last ?>" />
                              </td>
                            </tr>
                            <tr>
                              <td class="style6">Firstname</td>
                              <td class="style6">
                                <input type="text" name="doc_first" id="doc_first" value="<?= $doc_first ?>" />
                              </td>
                            </tr>
                            <tr>
                              <td class="style6">Middlename</td>
                              <td class="style6">
                                <input type="text" name="doc_mi" id="doc_mi" value="<?= $doc_mi ?>" />
                              </td>
                            </tr>
                            <tr>
                              <td class="style6">Role</td>
                              <td class="style6">
                                <input type="text" name="dept_id" id="dept_id" value="<?= $dept_id ?>" />
                              </td>
                            </tr>
                            <tr>
                              <td class="style6">Department</td>
                              <td class="style6">
                                <input type="text" name="dept_name" id="dept_name" value="<?= $dept_name ?>" />
                              </td>
                            </tr>
                            <tr>
                              <td class="style6">Doctor's Fee </td>
                              <td class="style6">
                                <input type="text" name="doc_fee" id="doc_fee" value="<?= $doc_fee ?>" />
                              </td>
                            </tr>
                          </table>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table><br />
              <table width="60%" border="0" cellspacing="1" cellpadding="0">
                <tr>
                  <td bgcolor="#B6C0CB">
                    <table width="100%" border="0" cellspacing="1" cellpadding="0">
                      <tr>
                        <td bgcolor="#FFFFFF">
                          <table width="100%" border="0" cellspacing="2" cellpadding="0">
                            <tr>
                              <td width="38%" class="style1 style10">Total Charge </td>
                              <td width="62%">
                                <input name="total_coverage" type="text" class="style6" id="total_coverage" value="<?= $total_coverage ?>" />
                              </td>
                            </tr>
                          </table>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  <br />
  <br />
  <br />
</form>
</body>
</html>
