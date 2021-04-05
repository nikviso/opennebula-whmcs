<div class="overview-vm">
    <h3>{$LANG.clientareaproductdetails}</h3>

    <div  class="row-vm">
        <div class="prod-details-vm">
            <div class="row">
                <div class="col-sm-5">
                    {$LANG.clientareahostingregdate}
                </div>
                <div class="col-sm-7 bold">
                    {$regdate}
                </div>
            </div>

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
                    {$LANG.orderpaymentmethod}
                </div>
                <div class="col-sm-7 bold">
                    {$paymentmethod}
                </div>
            </div>

            <div class="row">
                <div class="col-sm-5">
                    {$LANG.firstpaymentamount}
                </div>
                <div class="col-sm-7 bold">
                    {$firstpaymentamount}
                </div>
            </div>

            <div class="row">
                <div class="col-sm-5">
                    {$LANG.recurringamount}
                </div>
                <div class="col-sm-7 bold">
                    {$recurringamount}
                </div>
            </div>

            <div class="row">
                <div class="col-sm-5">
                    {$LANG.clientareahostingnextduedate}
                </div>
                <div class="col-sm-7 bold">
                    {$nextduedate}
                </div>
            </div>

            <div class="row">
                <div class="col-sm-5">
                    {$LANG.orderbillingcycle}
                </div>
                <div class="col-sm-7 bold">
                    {$billingcycle}
                </div>
            </div>

            <div class="row">
                <div class="col-sm-5">
                    {$LANG.clientareastatus}
                </div>
                <div class="col-sm-7 bold uppercase" style="color:{if $suspendreason || $productstatuscancelled}#87939F{else}#3FAD46{/if}">
                    {$status}
                </div>
            </div>

            {if $suspendreason}
                <div class="row">
                    <div class="col-sm-5">
                        {$LANG.suspendreason}
                    </div>
                    <div class="col-sm-7 bold">
                        {$suspendreason}
                    </div>
                </div>
            {/if}
            
        </div>

    </div>
</div>

{if ! $suspendreason && ! $productstatuscancelled}
    <div id="tkvm" class="hidden">{$token}</div>
    <div class="alert-raw-vm">
        <div id="alert" class="alert text-center"></div>
    </div>
{/if}

<hr>
<div class="row">
    {if ! $suspendreason && ! $productstatuscancelled}
    <div class="col-sm-4">
        <form method="post" action="clientarea.php?action=productdetails">
            <input type="hidden" name="id" value="{$serviceid}" />
            <input type="hidden" name="customAction" value="manage" />
            <button type="submit" class="btn btn-default btn-block">
                {$LANG.vpsmanagement}
            </button>
        </form>
    </div>
    {/if}

    {if ! $productstatuscancelled}
    <div class="col-sm-4">
        <a href="clientarea.php?action=cancel&amp;id={$id}" class="btn btn-danger btn-block {if $pendingcancellation}disabled{/if}">
            {if $pendingcancellation}
                {$LANG.cancellationrequested}
            {else}
                {$LANG.cancel}
            {/if}
        </a>
    </div>
    {/if}
</div>

   
