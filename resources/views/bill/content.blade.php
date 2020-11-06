<p class="ql-align-right" style="text-align:{bill_settings.logo_position};min-height:{bill_settings.logo_height}">{logo}</p>
<p><br></p>
<p><br></p>
<p class="ql-align-right">Telefon ............ {phone}</p>
<p class="ql-align-right">E-Mail ............ {email}</p>
<p class="ql-align-right">Web ................ <a href="{web}" target="_blank">{web}</a></p>
<p><br></p>
<p class="ql-align-justify"><u>{author}, {street}, {city}</u></p>
<p><br></p>
<p style="font-size:13px;">{customer.name}</p>
<p style="font-size:13px;">{customer.contact}</p>
<p style="font-size:13px;">{customer.street} {customer.house}</p>
<p style="font-size:13px;">{customer.postcode} {customer.city}</p>
<p style="font-size:13px;">{customer.country}</p>
<p><br></p>
<p><br></p>
<p><br></p>
<p><span class="ql-size-large">Rechnung #{bill.invoice_number} vom {bill.created_at} - Kunden-Nr. {customer.id}</span></p>
<p><br></p>
<p>Sehr geehrte Damen und Herren!</p>
<p><br></p>
<p>Für unsere Tätigkeiten erlauben wir uns Ihnen folgendes zu berechnen:</p>
<p><br></p>
<p>{TABLE}</p>
<p><br></p>
<p>Bitte zahlen Sie ohne Abzüge innerhalb von 14 Tagen. Bei absehbarem Verzug bitte bei uns melden.</p>
<p>Das Leistungsdatum entspricht dem Rechnungsdatum, soweit nicht anders angegeben.</p>
<p><br></p>
<p>Mit freundlichem Gruß</p>
<p><br></p>
<p>{author}</p>
<p>Mechthild Korte - Buchhaltung</p>
<p><br></p>
<p>E-Mail: {email}</p>
<p>Telefon: {phone} (Montag+Dienstag von 9-15 Uhr)</p>

<div id="footer-container">
    <table border="0" width="100%">
        <tr>
            <td width="20%">{author}</td>
            <td width="30%">AG Osnabrück HRB 131351</td>
            <td width="30%">Bank: Kreissparkasse Nordhorn</td>
            <td width="20%">Rechnung #{bill.invoice_number}</td>
        </tr>
        <tr>
            <td width="20%">{street}</td>
            <td width="30%">UST-ID DE219327265</td>
            <td width="30%">IBAN: DE22267500010005026588</td>
            <td width="20%">vom {bill.created_at}</td>
        </tr>
        <tr>
            <td width="20%">DE-48529 {city}</td>
            <td width="30%">GF: Dirk Aldekamp</td>
            <td width="30%">BIC: NOLADE21NOH</td>
            <td width="20%"></td>
        </tr>
    </table>
</div>

