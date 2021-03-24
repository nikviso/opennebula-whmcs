<div class="overview">
<h3>{$LANG.clientareaproductdetails}</h3>

  <div class="row prod-details">
    <div class="col-md-6">

      <table class="table table-striped">
        <tbody>
          <tr>
            <td>{$LANG.clientareahostingregdate}</td>
            <td class="bold">{$regdate}</td>
          </tr>
          <tr>
            <td>{$LANG.orderproduct}</td>
            <td class="bold">{$groupname} - {$product}</td>
          </tr>
          {if $type eq "server"}
              {if $domain}
                <tr>
                  <td>{$LANG.serverhostname}</td>
                  <td class="bold">{$domain}</td>
                </tr>
              {/if}
              {if $dedicatedip}
                <tr>
                  <td>{$LANG.primaryIP}</td>
                  <td class="bold">{$dedicatedip}</td>
                </tr>
            {/if}
            {if $assignedips}
              <tr>
                <td>{$LANG.assignedIPs}</td>
                <td class="bold">{$assignedips|nl2br}</td>
              </tr>
            {/if}
            {if $ns1 || $ns2}
              <tr>
                <td>{$LANG.domainnameservers}</td>
                <td class="bold">{$ns1}<br />{$ns2}</td>
              </tr>
            {/if}
          {else}
            {if $domain}
              <tr>
                <td>{$LANG.orderdomain}</td>
                <td class="bold">{$domain}
                <a href="http://{$domain}" target="_blank" class="btn btn-default btn-xs">{$LANG.visitwebsite}</a></td>
              </tr>
            {/if}
            {if $extraVariable2}
              <tr>
                <td>{$LANG.serverusername}</td>
                <td class="bold">{$user_email}</td>
              </tr>
            {/if}
            {if $serverdata}
              <tr>
                <td>{$LANG.servername}</td>
                <td class="bold">{$serverdata.hostname}</td>
              </tr>
              <tr>
                <td>{$LANG.domainregisternsip}</td>
                <td class="bold">{$serverdata.ipaddress}</td>
              </tr>
              {if $serverdata.nameserver1 || $serverdata.nameserver2 || $serverdata.nameserver3 || $serverdata.nameserver4 || $serverdata.nameserver5}
              <tr>
                <td>{$LANG.domainnameservers}</td>
                <td class="bold">
                  {if $serverdata.nameserver1}{$serverdata.nameserver1} ({$serverdata.nameserver1ip})<br />{/if}
                  {if $serverdata.nameserver2}{$serverdata.nameserver2} ({$serverdata.nameserver2ip})<br />{/if}
                  {if $serverdata.nameserver3}{$serverdata.nameserver3} ({$serverdata.nameserver3ip})<br />{/if}
                  {if $serverdata.nameserver4}{$serverdata.nameserver4} ({$serverdata.nameserver4ip})<br />{/if}
                  {if $serverdata.nameserver5}{$serverdata.nameserver5} ({$serverdata.nameserver5ip})<br />{/if}
                </td>
              </tr>
              {/if}
          {/if}
        {/if}
        {if $dedicatedip}
        <tr>
          <td>{$LANG.domainregisternsip}</td>
          <td class="bold">{$dedicatedip}</td>
        </tr>
        {/if}
        {foreach from=$configurableoptions item=configoption}
        <tr>
          <td>{$configoption.optionname}</td>
          <td class="bold configoption">
            {if $configoption.optiontype eq 3}
            {if $configoption.selectedqty}
            {$LANG.yes}
            {else}
            {$LANG.no}
            {/if}
            {elseif $configoption.optiontype eq 4}
            {$configoption.selectedqty} x {$configoption.selectedoption}
            {else}
            {$configoption.selectedoption}
            {/if}
          </td>
        </tr>
        {/foreach}
        {foreach from=$productcustomfields item=customfield}
        <tr>
          <td>{$customfield.name}</td>
          <td class="bold">{$customfield.value}</td>
        </tr>
        {/foreach}
        {if $lastupdate}
        <tr>
          <td>{$LANG.clientareadiskusage}</td>
          <td class="bold">{$diskusage}MB / {$disklimit}MB ({$diskpercent})</td>
        </tr>
        <tr>
          <td>{$LANG.clientareabwusage}</td>
          <td class="bold">{$bwusage}MB / {$bwlimit}MB ({$bwpercent})</td>
        </tr>
        {/if}
        </tbody>
      </table>
    </div>
    <div class="col-md-6">
      <table class="table table-striped">
        <tbody>
          <tr>
            <td>{$LANG.orderpaymentmethod}</td>
            <td class="bold">{$paymentmethod}</td>
          </tr>
          <tr>
            <td>{$LANG.firstpaymentamount}</td>
            <td class="bold">{$firstpaymentamount}</td>
          </tr>
          <tr>
            <td>{$LANG.recurringamount}</td>
            <td class="bold">{$recurringamount}</td>
          </tr>
          <tr>
            <td>{$LANG.clientareahostingnextduedate}</td>
            <td class="bold">{$nextduedate}</td>
          </tr>
          <tr>
            <td>{$LANG.orderbillingcycle}</td>
            <td class="bold">{$billingcycle}</td>
          </tr>
          <!-- <tr>
            <th scope="row">3</th>
            <td>{$LANG.clientareastatus}</td>
            <td class="bold">{$status}</td>
          </tr> -->
          <!-- <tr>
            <th scope="row">3</th>
            <td>{$LANG.clientareaserverstatus}</td>
            <td class="bold">{$serverstatus}</td>
          </tr> -->
          <!-- {if $suspendreason}
            <tr>
              <th scope="row">3</th>
              <td>{$LANG.suspendreason}</td>
              <td class="bold">{$suspendreason}</td>
            </tr>
          {/if} -->
        </tbody>
      </table>
    </div>
  </div>
  <div class="active">
    <p>{$LANG.clientareastatus} <span class="uppercase">{$status}</span></p>
  </div>
</div>
<div class="vpsmanagement">
<div class="inline-block">
  <h4>VPS Management</h4>
  <span class="float-right italic ajax-status">{$serverstatus}</span>
  <span class="circle {$statusbulb}"></span>
  <!-- <span class="circle red"></span> -->
  <span class="float-right italic">{$LANG.clientareastatus}</span>
  {if $suspendreason}
  <span class="float-right">{$LANG.suspendreason}</span>
  <span class="icon"></span> <span>{$suspendreason}</span>

  {/if}
</div>
<div class="row">
    {if $suspendreason}
        <div>
            <div class="col-sm-5">
                {$LANG.suspendreason}
            </div>
            <div class="col-sm-7">
                {$suspendreason}
            </div>
        </div>
    {/if}
  </div>
  {if not $suspendreason}
  <div class="row loading-animation">
    <div id="wait" class='loader__wrapper'>
      <div class="loading">
        <div></div>
        <div></div>
        <div></div>
      </div>
    </div>
      <div class="col-sm-4 select-wrapper">
        <p class="text-grey text-height"></p>
        <span class="dropdown-el reboot {if $action != "Poweroff"}disabled{/if}">
          <label for="reboot-vps">Reboot VPS</label><input type="radio" name="sortType" value="Relevance" checked="checked" id="reboot-vps">
          <label class="vm-command-button" data-id="{$serviceid}" data-action="Reboot" type="submit" value="Reboot" for="reboot-vps">Reboot VPS</label><input type="radio" name="sortType" value="Relevance" checked="checked" id="reboot-vps">
          <label class="vm-command-button" data-id="{$serviceid}" data-action="RebootHard" type="submit" value="RebootHard" for="reboot-hard">Reboot hard</label><input type="radio" name="sortType" value="Popularity" id="reboot-hard">
        </span>
      </div>
      <div class="col-sm-4 select-wrapper">
        <p class="text-grey text-height"></p>
        <span class="dropdown-el poweroff">
          <label for="power-vps">Power Off VPS</label><input type="radio" name="sortType" value="Relevance" checked="checked" id="power-vps">
          {if $action == "Poweroff"}
          <label class="vm-command-button poweroffchange" data-id="{$serviceid}" data-action="PowerOff" type="submit" value="Poweroff" for="power-off">Power Off</label><input type="radio" name="sortType" value="Popularity" id="power-off">
          <label class="vm-command-button poweroffchange" data-id="{$serviceid}" data-action="PowerOffHard" type="submit" value="PowerOffHard"  for="power-off-hard">Power Off Hard</label><input type="radio" name="sortType" value="Popularity" id="power-off-hard">
          <label class="vm-command-button poweroffchange {if $action == "Poweroff"}hidden{/if}" data-id="{$serviceid}" data-action="ClientResume" type="submit" value="ClientResume"  for="resume">Resume</label><input type="radio" name="sortType" value="Popularity" id="resume">
          {else}
          <label class="vm-command-button poweroffchange {if $action != "Poweroff"}hidden{/if}" data-id="{$serviceid}" data-action="PowerOff" type="submit" value="Poweroff" for="power-off">Power Off</label><input type="radio" name="sortType" value="Popularity" id="power-off">
          <label class="vm-command-button poweroffchange {if $action != "Poweroff"}hidden{/if}" data-id="{$serviceid}" data-action="PowerOffHard" type="submit" value="PowerOffHard"  for="power-off-hard">Power Off Hard</label><input type="radio" name="sortType" value="Popularity" id="power-off-hard">
          <label class="vm-command-button poweroffchange" data-id="{$serviceid}" data-action="ClientResume" type="submit" value="ClientResume"  for="resume">Resume</label><input type="radio" name="sortType" value="Popularity" id="resume">
          {/if}
        </span>
      </div>
      <div class="col-sm-4 select-wrapper">
        <p class="text-grey text-height">Rebuild your VPS</p>
        <span class="dropdown-el rebuild {if $action != "Poweroff"}disabled{/if}">
          <label for="select-your-os">Select your OS</label><input type="radio" name="sortType" value="Relevance" checked="checked" id="select-your-os">
          {foreach from=$osnames item=os}
          <label class="vm-command-button" data-action="ClientRebuild" data-os="{$os.name}" data-osid="{$os.id}" data-id="{$serviceid}" data-params="{$params['pid']}" for="">{$os.name}</label><input type="radio" name="sortType" value="Popularity" id="">
          {/foreach}
        </span>
      </div>
    </div>
    {/if}
    <div class="row">
      <div class="col-sm-12 cancellation">
        <h4>Product cancellation</h4>
      </div>
      <div class="col-sm-4 select-wrapper">
          <a href="clientarea.php?action=cancel&amp;id={$id}" class="btn btn-danger cancel-btn btn-block{if $pendingcancellation}disabled{/if}">
              {if $pendingcancellation}
                  {$LANG.cancellationrequested}
              {else}
                  {$LANG.cancel}
              {/if}
          </a>
      </div>
    </div>
    <div class="row">
      {if $packagesupgrade}
          <div class="col-sm-4">
              <a href="upgrade.php?type=package&amp;id={$id}" class="btn btn-success btn-block">
                  {$LANG.upgrade}
              </a>
          </div>
      {/if}
    </div>
<script>
// $(document).ready(function(){
//   CheckStatus();
// });


$('.dropdown-el').click(function(e) {
  e.preventDefault();
  e.stopPropagation();
  $(this).toggleClass('expanded');
  $('#'+$(e.target).attr('for')).prop('checked',true);
});
$(document).click(function() {
  $('.dropdown-el').removeClass('expanded');
});

(function($){
    $(document).ready(function(){
      CheckStatus()
    });
})(jQuery);


  //run Poweroff/Resume or Reboot on click
  $(".vm-command-button").on("click", function() {
    var $this = $(this);
    var action = $(this).attr('data-action');
    console.log(action);
    var newaction = '';
    var statecompare = '';
    var selected_os = '';
    var selected_osid = '';
    if(action == "Reboot"){
      newaction = "Reboot";
      statecompare = 'ACTIVE';
    }
    else if(action == "RebootHard"){
      newaction = "Reboot Hard";
      statecompare = 'ACTIVE';
    }
    else if(action == "PowerOff"){
      newaction = "ClientResume";
      statecompare = 'POWEROFF';
    }
    else if(action == "PowerOffHard"){
      newaction = "ClientResume";
      statecompare = 'POWEROFF';
    } else if(action == "ClientRebuild"){
      ajaxStart();
      newaction = "ClientRebuild";
      statecompare = 'ACTIVE';
      selected_os = $this.attr('data-os');
      selected_osid = $this.attr('data-osid');
    } else {
      newaction = "PowerOff";
      statecompare = 'ACTIVE';
    }
    runAjaxCommands(action, newaction, statecompare, $this, selected_os, selected_osid);

  });

  //Check if VM status is changed
  function compareData(action, newaction, statecompare, $this){
    ajaxStart();
    console.log('repeat', statecompare);
    var checkstate = CheckStatus($this);
    setTimeout(function () {
      if (checkstate != statecompare){
        console.log(checkstate,'not equal');
        compareData(action, newaction, statecompare, $this);
      }else{
        ajaxComplete();
        console.log(checkstate,'is equal');
        var msg = newaction;
        console.log(newaction);

        if(action == "Reboot"){
          console.log("is rebooted");
        } else if(action == "RebootHard"){
          console.log("is rebooted hard");
        } else if(action == "PowerOff"){
          $('.circle').removeClass('green').addClass('red');
          $('.ajax-status').html(statecompare);
          $($this).html(newaction).attr('data-action', newaction);
          $('.rebuild, .reboot').addClass('disabled');
          $('.poweroffchange').toggleClass('hidden');
        } else if(action == "PowerOffHard"){
          $('.circle').removeClass('green').addClass('red');
          $('.ajax-status').html(statecompare);
          $($this).html(newaction).attr('data-action', newaction);
          $('.rebuild, .reboot').addClass('disabled');
          $('.poweroffchange').toggleClass('hidden');
        } else if(action == "ClientRebuild"){
          newaction = "ClientRebuild";
          selected_os = $this.attr('data-os');
          selected_osid = $this.attr('data-osid');
          $($this).html(newaction).attr('data-action', newaction);
          $('.configoption').html(selected_os);
          console.log("Rebuild");
        } else {
          newaction = "PowerOff";
          statecompare = 'ACTIVE';
          $('.circle').removeClass('red').addClass('green');
          $('.ajax-status').html(statecompare);
          $($this).html(newaction).attr('data-action', newaction);
          $('.rebuild, .reboot').removeClass('disabled');
          $('.poweroffchange').toggleClass('hidden');
        }
      }
    }, 5000);
  }


 //Send command to VM
 function runAjaxCommands(action, newaction, statecompare, $this, selected_os, selected_osid){
   $.ajax({
     type: "POST",
     data: {
       "id": $($this).attr('data-id'),
       "modop":"custom",
       "a": action,
       "os_select": selected_os,
       "osid_select": selected_osid,
       "configoption3": "on",
       "token": $("#status-token").val(),
       "pid": $($this).attr('data-params')
     },
     url: "./function_call.php",
       //dataType: "json",
       success: function(result){
       compareData(action, newaction, statecompare, $this);
         // return result;
     },
     error: function (jqXHR, exception) {
       var msg = '';
       if (jqXHR.status === 0) {
     msg = 'Not connect.\n Verify Network.';
       } else if (jqXHR.status == 404) {
     msg = 'Requested page not found. [404]';
       } else if (jqXHR.status == 500) {
     msg = 'Internal Server Error [500].';
       } else if (exception === 'parsererror') {
     msg = 'Requested JSON parse failed.';
       } else if (exception === 'timeout') {
     msg = 'Time out error.';
       } else if (exception === 'abort') {
     msg = 'Ajax request aborted.';
       } else {
     msg = 'Uncaught Error.\n' + jqXHR.responseText;
       }
       $('#test').html(msg);
     },
   });
 }
//send status request to VM
function CheckStatus($this=null){
  if($this==null){
    console.log('page is loaded');
  }else{

  var dataresult = null;
  var test = $($this).attr('data-id');
  var token = $("#status-token").val();
  // console.log(test);
  // console.log(token, 'token');
      $.ajax({
        type: "POST",
        data: {
          "id": $($this).attr('data-id'),
          "modop":"custom",
          "check": "checkstatus",
          "token": token,
        },
        async: false,
        url: "./function_call.php",
          //dataType: "json",
          success: function(result){
	  			// var msg = result;
          console.log(result,'statuss ir');
	  			// $('#test').html(result).attr('data-action', newaction);
          dataresult = result;
          console.log(dataresult, 'checkdataresult');
        },
		    error: function (jqXHR, exception) {
				  var msg = '';
				  if (jqXHR.status === 0) {
				msg = 'Not connect.\n Verify Network.';
				  } else if (jqXHR.status == 404) {
				msg = 'Requested page not found. [404]';
				  } else if (jqXHR.status == 500) {
				msg = 'Internal Server Error [500].';
				  } else if (exception === 'parsererror') {
				msg = 'Requested JSON parse failed.';
				  } else if (exception === 'timeout') {
				msg = 'Time out error.';
				  } else if (exception === 'abort') {
				msg = 'Ajax request aborted.';
				  } else {
				msg = 'Uncaught Error.\n' + jqXHR.responseText;
				  }
				  $('#test').html(msg);
				},
      });
      return dataresult;
    }
    }
    //Loading start stop functions
    function ajaxStart(){
       $("#wait").fadeIn();
     }
    function ajaxComplete(){
      $("#wait").fadeOut();
    }

</script>
<input type="hidden" id="status-token" name="status_token" value="{$token}" />
