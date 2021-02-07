<h2>VPS Management</h2>
<!--
<p>This is an example of an additional custom page within a module's client area product management pages.</p>

<p>Everything that is available in the overview is also available in this template file along with any custom defined template variables.</p>
-->
<hr>

<div class="row">
    <div class="col-sm-5">
        {$LANG.orderproduct}
    </div>
    <div class="col-sm-7">
        {$groupname} - {$product}
    </div>
</div>

<!--
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
</div>
-->

<div class="row">
    <div class="col-sm-5">
        {$LANG.vmclientareastatus}
    </div>
    <div id="vmstate" class="col-sm-7">
        
    </div>
</div>

<script type="text/javascript">
refresh(0)
/*
$(document).ready(function() {
    var pageRefresh = 5000; //5 s
    refInt = setInterval(function() {
            refresh(refInt);
        }, pageRefresh);
});
*/
function startrefresh() {
    var pageRefresh = 5000; //5 s
    refInt = setInterval(function() {
                refresh(refInt);
            }, pageRefresh);
}

function refresh(refInt) {
    $.ajax({
    url: '/auxiliary.php?id={$id}&a=get_vm_state&token={$token}',
    method: "GET",
    dataType: "html",
    success: function(response) {
        state = $('#vmstate').text();
        //alert(refInt);
        if (response == 'POWEROFF') {
            $('#vmstate').html('<p style="color:red;">'+response+'</p>');
        } else if (response == 'ACTIVE') {
            $('#vmstate').html('<p style="color:green;">'+response+'</p>');
        } else {
            $('#vmstate').html('<p style="color:#CC9933;">'+response+'</p>');
        }
        /*
        if (response != state || refInt == 0 ) {
            clearInterval(refInt);
        }
        */
    } 
    });
}

</script>

<hr>

<div class="row">
    <div class="col-sm-4">
        <form method="post" action="clientarea.php?action=productdetails">
            <input type="hidden" name="id" value="{$serviceid}" />
            <button type="submit" class="btn btn-default btn-block">
                <i class="fa fa-arrow-circle-left"></i>
                Back to Overview
            </button>
        </form>
    </div>

    {if ! $suspendreason}
    <div class="col-sm-4">
        <a href="/clientarea.php?action=productdetails&amp;id={$id}&amp;modop=custom&amp;a=poweroff_vm&customAction=manage" class="btn btn-success btn-block{if $pendingcancellation}disabled{/if}" onclick="startrefresh()">
            {$LANG.poweroffvps}
        </a>
    </div>
    
    <div class="col-sm-4">
        <a href="/clientarea.php?action=productdetails&amp;id={$id}&amp;modop=custom&amp;a=resume_vm&customAction=manage" class="btn btn-success btn-block{if $pendingcancellation}disabled{/if}" onclick="startrefresh()">
            {$LANG.resumevps}
        </a>
    </div>
    {/if}    
</div>

