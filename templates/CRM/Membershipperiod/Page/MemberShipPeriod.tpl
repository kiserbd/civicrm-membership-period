<table>
    <thead>
        <tr>
    <th>Period</th>
    <th>Start Date</th>
    <th>End Date</th>
    <th>Contribution</th>
    </tr>
    </thead>
    <tbody>
        {$i=0}
        {foreach name=navLoop from= $membershipperiods item=membershipperiod}
            <tr class="{cycle values='odd,even'}-row crm-report">
                <td>#{$smarty.foreach.navLoop.iteration}</td>
                <td>{$membershipperiod.start_date|date_format}</td>
                <td>{$membershipperiod.end_date|date_format}</td>
                <td>
                  {if $membershipperiod.total_contribution_amount}
                    {$membershipperiod.total_contribution_amount|crmMoney:$membershipperiod.contribution_currency}<br>
                    <a href="{$membershipperiod.contribution_url}" class="action-item crm-hover-button">View Details</a>
                  {else}
                      --
                      {/if}
                </td>
            </tr>
        {/foreach}
    </tbody>
    
</table>