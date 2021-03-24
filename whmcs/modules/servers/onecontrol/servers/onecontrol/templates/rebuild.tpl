<h2>Rebuild VPS Test page</h2>

<h3>{$LANG.clientareaproductdetails}</h3>
<hr>

<div class="row">
    <div class="col-sm-5">
        {$LANG.orderproduct}
    </div>
    <div class="col-sm-7">
        {$groupname} - {$product}
    </div>
</div>

<div class="row">
    <div class="col-sm-5">
        Extra Variable 1
    </div>
    <div class="col-sm-7">
        {$extraVariable1}
    </div>
</div>

<div class="row">
    <div class="col-sm-5">
        Extra Variable 2
    </div>
    <div class="col-sm-7">
        {$extraVariable2}
    </div>


    <!-- <a href="clientarea.php?action=rebuild&amp;id={$id}" class="btn btn-danger btn-block{if $pendingrebuild}disabled{/if}">Rebuild</a> -->
</div>
<!-- <form method="post" action="clientarea.php?action=productdetails&amp;a=rebuild&amp;id={$id}&amp;os={$testvar}"> -->
<div class="row">
  <!-- <form method="post" action="clientarea.php?action=productdetails&customAction=rebuild&modop=custom&a=ClientRebuild&id={$id}&os={$testvar}"> -->
    <div class="col-sm-5">
        OS names
    </div>
    <div class="col-sm-7">
      <select div="os_select" class="oss" style="background: transparent;border:1px solid #ccc;">
        {foreach from=$osnames item=os}
        <option value="{$os.name}">{$os.name}</option>
        {/foreach}
      </select>
    </div>
    <input type="hidden" name="id" value="{$id}" />
    <input type="hidden" id="configoption3" name="configoption3" value="{$params['configoption3']}" />
    <input type="hidden" id="pid" name="pid" value="{$params['pid']}" />
    <br>
    <hr>
    <div id="rebuild-button" data-id="{$serviceid}" data-action="ClientRebuild" value="Rebuild" class="btn btn-default btn-block" style="width:25%;margin:0 auto; Margin-top: 35px;">
        <i class="fa fa-arrow-circle-left"></i>
        Rebuild
    </button>
  <!-- </form> -->
</div>


<!-- <input type="submit" id="gender" name="gender" value="poga" /> -->

<!-- <div id="people" border="1">
</div> -->
</div>

<script>

  //run Poweroff/Resume or Reboot on click
  $("#rebuild-button").on("click", function() {
    var $this = $(this);
    var action = $(this).attr('data-action');
    var newaction = '';
    var statecompare = '';
    var selected_os = $(".oss option:selected").val();
    if(action == "ClientRebuild"){
      newaction = "ClientRebuild";
      statecompare = 'ACTIVE'
    }

    runAjaxCommands(action, newaction, statecompare, $this, selected_os);
    compareData(action, newaction, statecompare, $this);
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
        $($this).html(newaction).attr('data-action', newaction);
      }
    }, 5000);
  }


 //Send command to VM
 function runAjaxCommands(action, newaction, statecompare, $this, selected_os){

   $.ajax({
     type: "POST",
     data: {
       "id": $($this).attr('data-id'),
       "modop":"custom",
       "a": action,
       "os_select": selected_os,
       "configoption3": $('#configoption3').val(),
       "pid": $('#pid').val()
     },
     url: "function_call.php",
       //dataType: "json",
       success: function(result){
         console.log(result);
         return result;
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
// //send status request to VM
// function CheckStatus($this){
//   var dataresult = null;
//       $.ajax({
//         type: "POST",
//         data: {
//           "id": $($this).attr('data-id'),
//           "modop":"custom",
//           "check": "checkstatus",
//         },
//         async: false,
//         url: "function_call.php",
//           //dataType: "json",
//           success: function(result){
// 	  			// var msg = result;
//           console.log(result,'statuss ir');
// 	  			// $('#test').html(result).attr('data-action', newaction);
//           dataresult = result;
//           console.log(dataresult, 'checkdataresult');
//         },
// 		    error: function (jqXHR, exception) {
// 				  var msg = '';
// 				  if (jqXHR.status === 0) {
// 				msg = 'Not connect.\n Verify Network.';
// 				  } else if (jqXHR.status == 404) {
// 				msg = 'Requested page not found. [404]';
// 				  } else if (jqXHR.status == 500) {
// 				msg = 'Internal Server Error [500].';
// 				  } else if (exception === 'parsererror') {
// 				msg = 'Requested JSON parse failed.';
// 				  } else if (exception === 'timeout') {
// 				msg = 'Time out error.';
// 				  } else if (exception === 'abort') {
// 				msg = 'Ajax request aborted.';
// 				  } else {
// 				msg = 'Uncaught Error.\n' + jqXHR.responseText;
// 				  }
// 				  $('#test').html(msg);
// 				},
//       });
//       return dataresult;
//     }
    //ikontas griezšanās funkcija
    function ajaxStart(){
       $("#wait").css("display", "block");
     }

    function ajaxComplete(){
      $("#wait").css("display", "none");
    }

    $("#checkstatus1").on("click", function() {
       CheckStatus()
     });

// $(document).ready(function(){
//   console.log('tests');
    $(document).ajaxStart(function(){
      $("#wait").css("display", "block");
    });
    $(document).ajaxComplete(function(){
      $("#wait").css("display", "none");
    });
// ajax gabals
    $("#gender").on("click", function() {
       var your_selected_value = $(".oss option:selected").val();
       console.log(your_selected_value,'your_selected_value');
      $.ajax({
        type: "POST",
        data: {
          // "gender": $("#gender").val(),
          "os_select": your_selected_value,
          "a":"ClientRebuild",
          "action":"productdetails",
          "customAction":"rebuild"
        },
        url: "clientarea.php",
        dataType: "json",
        success: function(result){
          console.log(result);
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
      $('#people').html(msg);
  },
      });
    });
//ajax gabals
// });
</script>
<div id="wait" style="display:none;width:69px;height:89px;border:1px solid black;position:absolute;top:50%;left:50%;padding:2px;">
  <img src='demo_wait.gif' width="64" height="64" /><br>Loading..</div>



<!-- <div class="row">
    <div class="col-sm-4"> -->
        <!-- <form method="post" action="clientarea.php?action=productdetails">
            <input type="hidden" name="id" value="{$serviceid}" />
            <button type="submit" class="btn btn-default btn-block">
                <i class="fa fa-arrow-circle-left"></i>
                Back to Overview
            </button>
        </form> -->
    <!-- </div>
</div> -->
