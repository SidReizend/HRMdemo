<style>
table {
    font-family: arial, sans-serif;
    border-collapse: collapse;
    width: 100%;
}

td, th {
    border: 1px solid #dddddd;
    text-align: left;
    padding: 8px;
}

tr:nth-child(even) {
    background-color: #dddddd;
}
</style>
<table>
    <tr>
        <th colspan="2" style="text-align: center">
            Final Settlement Sheet
        </th>
    </tr>
    <tr>
        <td>
            Talent
        </td>
        <td>
           <?php
           echo $resignCalculator->name;
           ?>
        </td>
    </tr>
    <tr>
        <td>
            Date of Resignation
        </td>
        <td>
           <?php
           echo $resignCalculator->till_date;
           ?>
        </td>
    </tr>
    <tr>
        <td>
            Monthly Salary
        </td>
        <td>
           <?php
           echo $resignCalculator->salary;
           ?>
        </td>
    </tr>
    <tr>
        <td>
            Current EL balance
        </td>
        <td>
           <?php
           echo $resignCalculator->el_left;
           ?>
        </td>
    </tr>
    <tr>
        <td>
            Excess Credited – EL
        </td>
        <td>
           <?php
           echo $resignCalculator->el_actual;
           ?>
        </td>
    </tr>
    <tr>
        <td>
            EL settlement
        </td>
        <td>
           <?php
           echo $resignCalculator->settlement_el;
           ?>
        </td>
    </tr>
    <tr>
        <td>
            Comp off left
        </td>
        <td>
           <?php
           echo $resignCalculator->comp_off;
           ?>
        </td>
    </tr>
    <tr>
        <td>
            Lop By Hr. Shortage
        </td>
        <td>
           <?php
           echo $resignCalculator->lop_by_shortage;
           ?>
        </td>
    </tr>
    <tr>
        <td>
            Lop By Absent
        </td>
        <td>
           <?php
           echo $resignCalculator->lop_by_absent;
           ?>
        </td>
    </tr>
    <tr>
        <td>
            Total Lop
        </td>
        <td>
           <?php
           echo $resignCalculator->total_lop;
           ?>
        </td>
    </tr>
    <tr>
        <td>
            No. of days Pay
        </td>
        <td>
           <?php
           echo $resignCalculator->days_pay;
           ?>
        </td>
    </tr>
    <tr>
        <td>
            Daily Pay
        </td>
        <td>
           <?php
           echo $resignCalculator->daily_pay;
           ?>
        </td>
    </tr>
    <tr>
        <td>
            El Settlement Pay
        </td>
        <td>
           <?php
           echo $resignCalculator->el_settlement_pay;
           ?>
        </td>
    </tr>
    <tr>
        <td>
            Claim Pending
        </td>
        <td>
           <?php
           echo $resignCalculator->claim_pending;
           ?>
        </td>
    </tr>
    <tr>
        <td>
            Incentive
        </td>
        <td>
           <?php
           echo $resignCalculator->insentive;
           ?>
        </td>
    </tr>
    <tr>
        <td>
            Total Settlement
        </td>
        <td>
           <?php
           echo $resignCalculator->total;
           ?>
        </td>
    </tr>
</table>

<p style="color: red">
    Note : <?php
           echo $resignCalculator->note;
           ?>
</p>