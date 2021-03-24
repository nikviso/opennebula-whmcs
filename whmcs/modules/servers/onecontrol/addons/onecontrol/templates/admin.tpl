<style>
body {font-family: Arial;}
/* Style the tab content */
.tabcontent {
  display: none;
  padding: 6px 12px;
  border: 1px solid #ccc;
  border-top: none;
}
</style>

<ul class="nav nav-tabs admin-tabs" role="tablist">
<li class="active"><a class="tab-top" href="#tab1" data-toggle="tab" id="{SHOWTABDEF}" onclick="openTab(event, 'SHOW')">Show VM</a></li>
<li><a class="tab-top" href="#tab2" data-toggle="tab" id="{ADDTABDEF}" onclick="openTab(event, 'ADD')">Add VM</a></li>
<li><a class="tab-top" href="#tab2" data-toggle="tab" id="{EDITTABDEF}" onclick="openTab(event, 'EDIT')">EDIT VM</a></li>
<li><a class="tab-top" href="#tab2" data-toggle="tab" id="{DELTABDEF}" onclick="openTab(event, 'DEL')">Del VM</a></li>
<li><a class="tab-top" href="#tab3" data-toggle="tab" id="{OSTABDEF}" onclick="openTab(event, 'OS')">Operating Systems</a></li>
<li><a class="tab-top" href="#tab4" data-toggle="tab" id="{ADDOSTABDEF}" onclick="openTab(event, 'ADDOS')">Add OS</a></li>
<li><a class="tab-top" href="#tab5" data-toggle="tab" id="{DELOSTABDEF}" onclick="openTab(event, 'DELOS')">Delete OS</a></li>
<!-- <li class=""><a class="tab-top" href="#tab3" data-toggle="tab" id="{CLEARTABDEF}" onclick="openTab(event, 'CLEAR')">Clear VM</a></li>
<li class=""><a class="tab-top" href="#tab4" data-toggle="tab" id="{DELTABDEF}" onclick="openTab(event, 'DELETE')">Delete VM</a></li> -->
</ul>

<div id="SHOW" class="tabcontent">
<!-- <form method="post" action="addonmodules.php?module=onecontrol">

<div class="btn-container">
    <button name="action" value="show" type="submit" class="btn btn-primary">Show</button>
</div> -->
<table class="table"><tr>
               <th>VM Name</th>
               <th>VM IP Adress</th>
               <th>VM Network mask</th>
               <th>VM Network address</th>
               <th>VM GW IP adress</th>
               <th>DNS IP adress</th>
               <th>Network ID</th>
               <th>VM active</th>
               </tr>
{VM}

</table>
</div>

<div id="OS" class="tabcontent">
  <table class="table"><tr>
                 <th>ID</th>
                 <th>OS</th>
                 <th>Product ID</th>
                 <th>Open Nebula Template ID</th>
                 </tr>
    {OS}
  </table>
</div>

<div id="DEL" class="tabcontent">
  <form method="post" action="addonmodules.php?module=onecontrol" enctype=multipart/form-data>
    <table class="form" width="100%" border="0" cellspacing="2" cellpadding="3">
        <tbody>
        <tr>
            <td width="20%" class="fieldlabel">VM NAME</td>
            <td class="fieldarea">
                <input type="text" name="vars[vm_name]" class="form-control input-225" value="{VM_NAME}" />
            </td>
        </tr>
        <tr>
            <td width="20%" class="fieldlabel">VM IP ADDRESS</td>
            <td class="fieldarea">
                <input type="text" name="vars[vm_ip_address]" class="form-control input-225" value="{VM_IP_ADDRESS}" />
            </td>
        </tr>
        <tr>
            <td width="20%" class="fieldlabel">VM NETWORK MASK</td>
            <td class="fieldarea">
                <input type="text" name="vars[vm_network_mask]" class="form-control input-225" value="{VM_NETWORK_MASK}" />
            </td>
        </tr>
        <tr>
            <td width="20%" class="fieldlabel">VM NETWORK ADDRESS</td>
            <td class="fieldarea">
                <input type="text" name="vars[vm_network_address]" class="form-control input-225" value="{VM_NETWORK_ADDRESS}" />
            </td>
        </tr>
        <tr>
            <td width="20%" class="fieldlabel">VM GATEWAY ADDRESS</td>
            <td class="fieldarea">
                <input type="text" name="vars[vm_gw_ip_address]" class="form-control input-225" value="{VM_GW_IP_ADDRESS}" />
            </td>
        </tr>
        <tr>
            <td width="20%" class="fieldlabel">VM DNS ADDRESS</td>
            <td class="fieldarea">
                <input type="text" name="vars[vm_dns_ip_address]" class="form-control input-225" value="{VM_DNS_IP_ADDRESS}" />
            </td>
        </tr>
        <tr>
            <td width="20%" class="fieldlabel">ONE NETWORK ID</td>
            <td class="fieldarea">
                <input type="text" name="vars[one_network_id]" class="form-control input-225" value="{ONE_NETWORK_ID}" />
            </td>
        </tr>
      </tbody>
    </table>

  <div class="btn-container">
      <button name="action" value="del" type="submit" class="btn btn-primary">DELETE</button>
  </div>
  </form>
</div>

<div id="EDIT" class="tabcontent">
  <form method="post" action="addonmodules.php?module=onecontrol" enctype=multipart/form-data>
    <table class="form" width="100%" border="0" cellspacing="2" cellpadding="3">
        <tbody>
        <tr>
            <td width="20%" class="fieldlabel">VM NAME</td>
            <td class="fieldarea">
                <input type="text" name="vars[vm_name]" class="form-control input-225" value="{VM_NAME}" />
            </td>
        </tr>
        <tr>
            <td width="20%" class="fieldlabel">VM IP ADDRESS</td>
            <td class="fieldarea">
                <input type="text" name="vars[vm_ip_address]" class="form-control input-225" value="{VM_IP_ADDRESS}" />
            </td>
        </tr>
        <tr>
            <td width="20%" class="fieldlabel">VM NETWORK MASK</td>
            <td class="fieldarea">
                <input type="text" name="vars[vm_network_mask]" class="form-control input-225" value="{VM_NETWORK_MASK}" />
            </td>
        </tr>
        <tr>
            <td width="20%" class="fieldlabel">VM NETWORK ADDRESS</td>
            <td class="fieldarea">
                <input type="text" name="vars[vm_network_address]" class="form-control input-225" value="{VM_NETWORK_ADDRESS}" />
            </td>
        </tr>
        <tr>
            <td width="20%" class="fieldlabel">VM GATEWAY ADDRESS</td>
            <td class="fieldarea">
                <input type="text" name="vars[vm_gw_ip_address]" class="form-control input-225" value="{VM_GW_IP_ADDRESS}" />
            </td>
        </tr>
        <tr>
            <td width="20%" class="fieldlabel">VM DNS ADDRESS</td>
            <td class="fieldarea">
                <input type="text" name="vars[vm_dns_ip_address]" class="form-control input-225" value="{VM_DNS_IP_ADDRESS}" />
            </td>
        </tr>
        <tr>
            <td width="20%" class="fieldlabel">ONE NETWORK ID</td>
            <td class="fieldarea">
                <input type="text" name="vars[one_network_id]" class="form-control input-225" value="{ONE_NETWORK_ID}" />
            </td>
        </tr>
      </tbody>
    </table>

  <div class="btn-container">
      <button name="action" value="edit" type="submit" class="btn btn-primary">EDIT</button>
      <button name="action" value="update" type="submit" class="btn btn-primary">upade</button>
  </div>
  </form>
</div>


<div id="ADDOS" class="tabcontent">
  <form method="post" action="addonmodules.php?module=onecontrol" enctype=multipart/form-data>
  <table class="form" width="100%" border="0" cellspacing="2" cellpadding="3">
      <tbody>
      <tr>
          <td width="20%" class="fieldlabel">OS</td>
          <td class="fieldarea">
              <input type="text" name="vars[os_name]" class="form-control input-225" value="{VM_NAME}" />
          </td>
      </tr>
      <tr>
          <td width="20%" class="fieldlabel">Product ID</td>
          <td class="fieldarea">
              <input type="text" name="vars[os_id]" class="form-control input-225" value="{VM_IP_ADDRESS}" />
          </td>
      </tr>
      <tr>
          <td width="20%" class="fieldlabel">ON Template ID</td>
          <td class="fieldarea">
              <input type="text" name="vars[os_template]" class="form-control input-225" value="{VM_NETWORK_MASK}" />
          </td>
      </tr>
  </tbody>
  </table>
  <div class="btn-container">
    <button name="action" value="addos" type="submit" class="btn btn-primary">Add</button>
  </div>
</div>

<div id="ADD" class="tabcontent">
  <form method="post" action="addonmodules.php?module=onecontrol" enctype=multipart/form-data>
    <table class="form" width="100%" border="0" cellspacing="2" cellpadding="3">
        <tbody>
        <tr>
            <td width="20%" class="fieldlabel">VM NAME</td>
            <td class="fieldarea">
                <input type="text" name="vars[vm_name]" class="form-control input-225" value="{VM_NAME}" />
            </td>
        </tr>
        <tr>
            <td width="20%" class="fieldlabel">VM IP ADDRESS</td>
            <td class="fieldarea">
                <input type="text" name="vars[vm_ip_address]" class="form-control input-225" value="{VM_IP_ADDRESS}" />
            </td>
        </tr>
        <tr>
            <td width="20%" class="fieldlabel">VM NETWORK MASK</td>
            <td class="fieldarea">
                <input type="text" name="vars[vm_network_mask]" class="form-control input-225" value="{VM_NETWORK_MASK}" />
            </td>
        </tr>
        <tr>
            <td width="20%" class="fieldlabel">VM NETWORK ADDRESS</td>
            <td class="fieldarea">
                <input type="text" name="vars[vm_network_address]" class="form-control input-225" value="{VM_NETWORK_ADDRESS}" />
            </td>
        </tr>
        <tr>
            <td width="20%" class="fieldlabel">VM GATEWAY ADDRESS</td>
            <td class="fieldarea">
                <input type="text" name="vars[vm_gw_ip_address]" class="form-control input-225" value="{VM_GW_IP_ADDRESS}" />
            </td>
        </tr>
        <tr>
            <td width="20%" class="fieldlabel">VM DNS ADDRESS</td>
            <td class="fieldarea">
                <input type="text" name="vars[vm_dns_ip_address]" class="form-control input-225" value="{VM_DNS_IP_ADDRESS}" />
            </td>
        </tr>
        <tr>
            <td width="20%" class="fieldlabel">ONE NETWORK ID</td>
            <td class="fieldarea">
                <input type="text" name="vars[one_network_id]" class="form-control input-225" value="{ONE_NETWORK_ID}" />
            </td>
        </tr>
      </tbody>
    </table>

  <div class="btn-container">
      <button name="action" value="add" type="submit" class="btn btn-primary">Add</button>
      <input type="checkbox" id="from_file" name="vars[from_file]" value="from_file" onclick="uploadEnable()" {checked}>
      <label for="from_file">From file</label>
      <label><input type="file" name="uploadfile" id="uploadfile" disabled /></label>
  </div>
  </form>
</div>

<div id="DELOS" class="tabcontent">
   <form method="post" action="addonmodules.php?module=onecontrol">
     <table class="form" width="100%" border="0" cellspacing="2" cellpadding="3">
    <tbody>
    <tr>
        <td width="20%" class="fieldlabel">ID</td>
        <td class="fieldarea">
            <input type="text" name="vars[os_id]" class="form-control input-225" value="" />
        </td>
    </tr>
</tbody>
</table>
<div class="btn-container">
    <button name="action" value="delos" type="submit" class="btn btn-primary">Delete</button>
</div>
   </form>

</div>

<div class="container">
<div class="{FEEDBACK_FILE_MESSAGE_CLASS}">
    <h4>{FEEDBACK_FILE_MESSAGE}</h4>
</div>
<div class="{FEEDBACK_MESSAGE_CLASS}">
    <h4>{FEEDBACK_MESSAGE}</h4>
</div>
</div>
<script>
function openTab(evt, tabName) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
      tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
      tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    document.getElementById(tabName).style.display = "block";
    evt.currentTarget.className += " active";
}

// Get the element with id="defaultOpen" and click on it
document.getElementById("tabShow").click();

function uploadEnable() {
    if (document.getElementById("uploadfile").disabled == true) {
        document.getElementById("uploadfile").disabled = false;
    } else {
        document.getElementById("uploadfile").disabled = true;
    }
}
</script>
