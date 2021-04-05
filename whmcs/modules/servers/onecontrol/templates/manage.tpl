{if ! $suspendreason && ! $productstatuscancelled}
<div class="overview-vm">

    <h3>{$LANG.vpsmanagement}</h3>

    <div  class="row-vm">
        <div class="prod-details-vm">
            <div class="row">
                <div class="col-sm-5">
                    {$LANG.orderproduct}
                </div>
                <div class="col-sm-7 bold">
                    {$groupname} - {$product}
                </div>
            </div>
            <div class="row">
                <div class="col-sm-5">
                    {$LANG.vmostype}
                </div>
                <div class="col-sm-7 bold">
                    {$vmostype}
                </div>
            </div>
            <div class="row">
                <div class="col-sm-5">
                    {$LANG.vmname}
                </div>
                <div class="col-sm-7 bold">
                    {$vmname}
                </div>
            </div>
            <div class="row">
                <div class="col-sm-5">
                    {$LANG.vmipaddress}
                </div>
                <div class="col-sm-7 bold">
                    {$vmipaddress}
                </div>
            </div>

            <div class="row">
                <div class="col-sm-5">
                    {$LANG.vmclientareastatus}
                </div>
                <div id="vmstate" class="col-sm-7 bold"  data-vmstate="status">
                    <div class='preloader-dots'>
                      <div class='dot'></div>
                      <div class='dot'></div>
                      <div class='dot'></div>
                      <div class='dot'></div>
                      <div class='dot'></div>
                    </div>        
                </div>
            </div>
        </div>
    </div>
    
</div>

<div id="tkvm" class="hidden">{$token}</div>
<div class="alert-row-vm">
    <div id="alert" class="alert-vm text-center"></div>
</div>

<script type="text/javascript">
refresh(0);

function startrefresh() {
    var pageRefresh = 5000;
    $('#vmstate').html('<div id="preloader" class="preloader-dots">');
    for (var i = 0; i < 5; i++) {
        $('#preloader').append('<div class="dot"></div>');
    }    
    refInt = setInterval(function() {
                refresh(refInt);
            }, pageRefresh);
}

function refresh(refInt) {
//console.log(response+' '+state);
    var state = $('#vmstate').attr('data-vmstate');
    $.ajax({
        url: '/auxiliary.php?id={$id}&a=get_vm_state&token='+$('#tkvm').text(),
        method: "GET",
        dataType: "html",
        success: function(response) {
            if (response != state) {
                if (response == 'POWEROFF') {
                    $('#vmstate').html('<div style="color:#FF0033;" class="bold">{$LANG.vmstatepoweroff}</div>');
                    $('#vmstate').attr('data-vmstate',response);
                    $('#resumevm').removeAttr('disabled','disabled');
                    $('#rebootvm').attr('disabled','disabled');
                    $('#poweroffvm').attr('disabled','disabled');
                } else if (response == 'ACTIVE') {
                    $('#vmstate').html('<div style="color:#3FAD46;" class="bold">{$LANG.vmstateactive}</div>');
                    $('#vmstate').attr('data-vmstate',response);
                    $('#resumevm').attr('disabled','disabled');
                    $('#rebootvm').removeAttr('disabled','disabled');
                    $('#poweroffvm').removeAttr('disabled','disabled');
                } else {
                    $('#vmstate').html('<div style="color:#CC9933;" class="bold">{$LANG.vmstateunknown}</div>');
                    $('#resumevm').attr('disabled','disabled');
                    $('#rebootvm').attr('disabled','disabled');
                    $('#poweroffvm').attr('disabled','disabled');                    
                }
                    $('#alert').html('');
                    $('#alert').removeClass('alert-success');
                    clearInterval(refInt);
            }    
        } 
    });
}

function action(action) {
    $.ajax({
        url: '/clientarea.php?action=productdetails&id={$id}&modop=custom&a='+action,
        method: "GET",
        dataType: "html",
        success: function(response) {
            $('#tkvm').html($(response).find('#tkvm').text());
            if ($(response).find('#alertModuleCustomButtonFailed').text()) {
                var alert = $(response).find('#alertModuleCustomButtonFailed').text();
                $('#alert').addClass('alert-danger');
                $('#alert').html(alert);
                return;
            } else if ($(response).find('#alertModuleCustomButtonSuccess').text()) {
                var alert = $(response).find('#alertModuleCustomButtonSuccess').text();
                $('#alert').addClass('alert-success');
                $('#alert').html(alert);
                    $('#rebootvm').attr('disabled','disabled');
                    $('#poweroffvm').attr('disabled','disabled');
                    $('#resumevm').attr('disabled','disabled');                    
                if (action == 'reboot_vm') {
                    $('#vmstate').attr('data-vmstate','state');
                    $('#rebootvm').attr('disabled','disabled');
                    $('#poweroffvm').attr('disabled','disabled');
                    $('#resumevm').attr('disabled','disabled');   
                    $('#vmstate').html('<div id="preloader" class="preloader-dots">');                    
                    for (var i = 0; i < 5; i++) {
                        $('#preloader').append('<div class="dot"></div>');
                    }
                    setTimeout(function(){           
                                $('#alert').html('<p></p>');
                                $('#alert').removeClass('alert-success');
                                refresh();
                              },30000); 
                } else {
                    startrefresh();
                }
            }
        } 
    });
}

function warning(action) {
        var button = document.getElementById("proceed");
        if (action == 'poweroffvm') {
            $('#warning_body').html('<h3>{$LANG.poweroffwarning}</h3>');
            button.setAttribute('onclick', 'action("poweroff_vm")');
        } else if (action == 'rebootvm') {
            $('#warning_body').html('<h3>{$LANG.rebootwarning}</h3>');
            button.setAttribute('onclick', 'action("reboot_vm")');
        } else {
            $('#warning_body').html('');
        }
    $("#warning_ModalLabel").modal();
}

</script>

<div class="modal fade" id="warning_ModalLabel" tabindex="-1" role="dialog" aria-labelledby="warning_ModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <div class="modal-title-w alert-w alert-danger-w" id="warning_ModalLabel"><b>{$LANG.titlevmwarning}</b></div>
      </div>
      <div id="warning_body"class="modal-body" align="center"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">{$LANG.closewarning}</button>
        <button id="proceed" type="button" class="btn btn-danger" data-dismiss="modal">{$LANG.proceedwarning}</button>
      </div>
    </div>
  </div>
</div>

<hr>
<div class="row">
    <div class="col-sm-4">
    <button id="poweroffvm" onclick="warning('poweroffvm')" class="btn btn-success btn-block {if $pendingcancellation}disabled{/if}">{$LANG.poweroffvps}</button>
     </div>
    <div class="col-sm-4">
    <button id="rebootvm" onclick="warning('rebootvm')" class="btn btn-success btn-block {if $pendingcancellation}disabled{/if}">{$LANG.rebootvps}</button>
    </div>
    <div class="col-sm-4">
    <button id="resumevm" onclick="action('resume_vm')" class="btn btn-success btn-block {if $pendingcancellation}disabled{/if}">{$LANG.resumevps}</button>
    </div>    
</div> 
{/if}
<hr>
<div class="row">
    <div class="col-sm-4">
        <form method="post" action="clientarea.php?action=productdetails">
            <input type="hidden" name="id" value="{$serviceid}" />
            <button type="submit" class="btn btn-default btn-block">
                <i class="fa fa-arrow-circle-left"></i>
                {$LANG.backtooverview}
            </button>
        </form>
    </div>
</div>     


