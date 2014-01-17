# Email Delivery

Command Line Tool For Emails Delivery.

## Options

<table>
    <tr>
        <td width="150">-db, -database</td>
        <td>SQLite Database File (Required). Default delivery.sqlite</td>
    </tr>
    <tr>
        <td>-e, -emails</td>
        <td>Comma Separated Receivers Emails List</td>
    </tr>
    <tr>
        <td>-ef, -emails-file</td>
        <td>Read Line By Line Receivers Emails List File (Watch emails.txt)</td>
    </tr>
    <tr>
        <td>-m, -message</td>
        <td>Delivery Message (Not Html)</td>
    </tr>
    <tr>
        <td>-mf, -messages-file</td>
        <td>Delivery Messages List File In PHP Array Manner (Watch messages.php)</td>
    </tr>
    <tr>
        <td>-start</td>
        <td>Starts Delivery In Queue Manner (If you not Clear Queue, then old (not sent before) messages will be sended). Flag Only. Default false</td>
    </tr>
    <tr>
        <td>name</td>
        <td>desc</td>
    </tr>
    <tr>
        <td>-cq, --clear-queue</td>
        <td>Clear Delivery Queue (Will Be Executed Before Adding New Emails In Queue). Flag Only. Default false</td>
    </tr>
    <tr>
        <td>-count</td>
        <td>Count Addresses In One Email. Default 5</td>
    </tr>
    <tr>
        <td>-pause</td>
        <td>Delay Between Emails Sending (In Seconds). Default 3</td>
    </tr>
	<tr>
        <td>-as, --add-senders</td>
        <td>Add To Senders List From File In PHP Array Manner (Watch senders.php). Will Be Added Only New Senders.</td>
    </tr>
</table>

Options -e (-ef) and -m (-mf) Should Be Specified When You Want Add Emails To Delivery Queue

## Example usage

```
> php delivery.php -as senders.php
```
add senders to senders list (in database) from senders.php

```
> php delivery.php -db delivery_new.sqlite -ef emails.txt -mf messages.php -cq -count 2 -pause 2 -start
```
set database delivery_new.sqlite adding new emails from combo emails.txt & messages.php files.
clear delivery queue before.
set count receivers in one email to 2.
set delay between sending emails to 2 seconds.
and finally start delivery.

```
> php delivery.php -start
```
just start delivery. useful when delivery was interrupted.