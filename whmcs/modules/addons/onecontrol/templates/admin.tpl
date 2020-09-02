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
<li class=""><a class="tab-top" href="#tab3" data-toggle="tab" id="{CLEARTABDEF}" onclick="openTab(event, 'CLEAR')">Clear VM</a></li>
<li class=""><a class="tab-top" href="#tab4" data-toggle="tab" id="{DELTABDEF}" onclick="openTab(event, 'DELETE')">Delete VM</a></li>
</ul>

<div id="SHOW" class="tabcontent">
<form method="post" action="addonmodules.php?module=onecontrol">
<table class="form" width="100%" border="0" cellspacing="2" cellpadding="3">
    <tbody>
    <tr>
        <td width="20%" class="fieldlabel">VM(NAME or ID)</td>
        <td class="fieldarea">
            <input type="text" name="vars[vmid]" class="form-control input-225" value="" />
        </td>
    </tr>
</tbody>
</table>
<div class="btn-container">
    <button name="action" value="show" type="submit" class="btn btn-primary">Show</button>
</div>
</form>

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

<div id="CLEAR" class="tabcontent">
  <h3>CLEAR</h3>
  <p>CLEAR.</p>
</div>

<div id="DELETE" class="tabcontent">
  <h3>DELETE</h3>
  <p>DELETE.</p>
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

