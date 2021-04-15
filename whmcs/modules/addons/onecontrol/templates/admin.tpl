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
<li><a class="tab-top" href="#tab2" data-toggle="tab" id="{EDITTABDEF}" onclick="openTab(event, 'EDIT')">Edit VM</a></li>
<li><a class="tab-top" href="#tab3" data-toggle="tab" id="{OSTABDEF}" onclick="openTab(event, 'OS')">Operating Systems</a></li>
<li><a class="tab-top" href="#tab4" data-toggle="tab" id="{ADDOSTABDEF}" onclick="openTab(event, 'ADDOS')">Add OS</a></li>
<li><a class="tab-top" href="#tab5" data-toggle="tab" id="{DELOSTABDEF}" onclick="openTab(event, 'DELOS')">Delete OS</a></li>
</ul>

<div id="SHOW" class="tabcontent">

<table class="table"><tr>
               <th>VM Name</th>
               <th>VM IP Adress</th>
               <th>VM Network mask</th>
               <th>VM Network address</th>
               <th>VM GW IP adress</th>
               <th>DNS IP adress</th>
               <th>Network ID</th>
               <th>Service ID</th>
               <th>One user ID</th>
               <th>VM ID</th>               
               </tr>
{VM}
</table>
</div>

<div id="OS" class="tabcontent">
  <table class="table"><tr>
                 <th>ID</th>
                 <th>OS</th>
                 <th>Image size(Gb)</th>
                 <th>ONE Template ID</th>
                 </tr>
{OS}
  </table>
</div>


<div id="EDIT" class="tabcontent">
  <form id="edit" method="post" action="addonmodules.php?module=onecontrol" enctype=multipart/form-data>
    <table class="form" width="100%" border="0" cellspacing="2" cellpadding="3">
        <tbody>
        <tr>
            <td width="20%" class="fieldlabel">VM NAME</td>
            <td class="fieldarea">
                <input id="vm_name" type="text" name="vars[vm_name]" class="form-control input-225" value="{VM_NAME}" />
            </td>
        </tr>
        <tr>
            <td width="20%" class="fieldlabel">VM IP ADDRESS</td>
            <td class="fieldarea">
                <input id="vm_ip_address" type="text" name="vars[vm_ip_address]" class="form-control input-225" value="{VM_IP_ADDRESS}" />
            </td>
        </tr>
        <tr>
            <td width="20%" class="fieldlabel">VM NETWORK MASK</td>
            <td class="fieldarea">
                <input id="vm_network_mask" type="text" name="vars[vm_network_mask]" class="form-control input-225" value="{VM_NETWORK_MASK}" />
            </td>
        </tr>
        <tr>
            <td width="20%" class="fieldlabel">VM NETWORK ADDRESS</td>
            <td class="fieldarea">
                <input id="vm_network_address" type="text" name="vars[vm_network_address]" class="form-control input-225" value="{VM_NETWORK_ADDRESS}" />
            </td>
        </tr>
        <tr>
            <td width="20%" class="fieldlabel">VM GATEWAY ADDRESS</td>
            <td class="fieldarea">
                <input id="vm_gw_ip_address" type="text" name="vars[vm_gw_ip_address]" class="form-control input-225" value="{VM_GW_IP_ADDRESS}" />
            </td>
        </tr>
        <tr>
            <td width="20%" class="fieldlabel">VM DNS ADDRESS</td>
            <td class="fieldarea">
                <input id="vm_dns_ip_address" type="text" name="vars[vm_dns_ip_address]" class="form-control input-225" value="{VM_DNS_IP_ADDRESS}" />
            </td>
        </tr>
        <tr>
            <td width="20%" class="fieldlabel">ONE NETWORK ID</td>
            <td class="fieldarea">
                <input id="one_network_id" type="text" name="vars[one_network_id]" class="form-control input-225" value="{ONE_NETWORK_ID}" />
            </td>
        </tr>
      </tbody>
    </table>

    <div class="btn-container">

        <div class="modal fade" id="delete_vm_Modal" tabindex="-1" role="dialog" aria-labelledby="delete_vm_ModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
                <div class="modal-title alert alert-danger" id="delete_vm_ModalLabel"><b>CHECK ONCE AGAIN!<b></div>

              </div>
              <div class="modal-body">
                <h1><b>Do You really want delete this VM parameters?</b></h1>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                <button type="submit" name="action" value="delete_vm" class="btn btn-danger">Delete</button>
              </div>
            </div>
          </div>
        </div>

        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#delete_vm_Modal">DELETE</button>
        <button name="action" value="find" type="submit" class="btn btn-primary">FIND by NAME</button>
        <button name="action" value="update" type="submit" class="btn btn-primary">UPDATE</button>
        <button name="action" value="add_vm" type="submit" class="btn btn-primary">ADD</button>
        <input type="checkbox" id="from_file" name="vars[from_file]" value="from_file" onclick="uploadEnable()" {checked}>
        <label for="from_file">From file</label>
        <label><input type="file" name="uploadfile" id="uploadfile" disabled /></label>      
        
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
              <input type="text" name="vars[os_name]" class="form-control input-225" />
          </td>
      </tr>
      <tr>
          <td width="20%" class="fieldlabel">Image size(Gb)</td>
          <td class="fieldarea">
              <input type="text" name="vars[image_size]" class="form-control input-225" />
          </td>
      </tr>
      <tr>
          <td width="20%" class="fieldlabel">ONE Template ID</td>
          <td class="fieldarea">
              <input type="text" name="vars[one_template_id]" class="form-control input-225" />
          </td>
      </tr>
  </tbody>
  </table>
  <div class="btn-container">
    <button name="action" value="addos" type="submit" class="btn btn-primary">Add</button>
  </div>
</div>

<div id="DELOS" class="tabcontent">
   <form method="post" action="addonmodules.php?module=onecontrol">
        <table class="form" width="100%" border="0" cellspacing="2" cellpadding="3">
            <tbody>
                <tr>
                    <td width="20%" class="fieldlabel">ID</td>
                    <td class="fieldarea">
                        <input type="text" name="vars[osid]" class="form-control input-225" value="" />
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="btn-container">
            <button name="action" value="delete_os" type="submit" class="btn btn-primary">Delete</button>
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
