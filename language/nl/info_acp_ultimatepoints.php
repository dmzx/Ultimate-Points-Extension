<?php
/**
*
* @package phpBB Extension - Ultimate Points
* @copyright (c) 2016 dmzx & posey - http://www.dmzx-web.net
* Nederlandse vertaling @ Solidjeuh <https://www.froddelpower.be>
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine
//
// Some characters you may want to copy&paste:
// ’ » “ ” …
//

// Merge the following language entries into the lang array
$lang = array_merge($lang, array(
	'ACP_POINTS'						=> 'Ultimate Points',
	'ACP_POINTS_BANK_EXPLAIN'			=> 'Hier kunt u de instellingen voor de Bank module wijzigen',
	'ACP_POINTS_BANK_TITLE'				=> 'Bank Instellingen',
	'ACP_POINTS_DEACTIVATED'			=> 'Ultimate Points is momenteel uitgeschakeld!',
	'ACP_POINTS_FORUM_EXPLAIN'			=> 'Hier kan je de standaard forum punten instellen voor alle forums tegelijk. Dus ideaal voor je eerste instellingen.<br />Houd u er rekening mee dat deze instellingen voor <strong>ALLE</strong> forum zijn. Dus als je manueel enkele van je forum instellingen hebt gewijzigd zal je deze opnieuw moeten doen na het gebruiken van deze optie.',
	'ACP_POINTS_FORUM_TITLE'			=> 'Forum Punten Instellingen',
	'ACP_POINTS_INDEX_EXPLAIN'			=> 'Hier kunt u de algemene instellingen van Ultimate Points wijzigen',
	'ACP_POINTS_INDEX_TITLE'			=> 'Punten Instellingen',
	'ACP_POINTS_LOTTERY_EXPLAIN'		=> 'Hier kunt u de instellingen van de Loterij module wijzigen',
	'ACP_POINTS_LOTTERY_TITLE'			=> 'Loterij Instellingen',
	'ACP_POINTS_ROBBERY_EXPLAIN'		=> 'Hier kunt u de instellingen van de Overval module wijzigen',
	'ACP_POINTS_ROBBERY_TITLE'			=> 'Overval Instellingen',
	'ACP_POINTS_VALUES_HINT'			=> '<strong>Hint: </strong>Voer altijd waarden in zonder scheidingsteken voor duizendtallen<br />en decimalen met een punt, Vb. 1000.50',
	'ACP_USER_POINTS_TITLE'				=> 'Ultimate Points Instellingen',
	'ACP_POINTS_MAINICON'				=> 'Selecteer hoofd pictogram',
	'ACP_POINTS_MAINICON_EXPLAIN'		=> 'Klik op naam om een nieuw Font Awesome icoon te selecteren.<br />Bekijk <samp><strong><a href="https://fontawesome.com/v4.7.0/icons/" title="Font Awesome">Font Awesome</a></strong></samp> voor meer info.',
	'ACP_POINTS_UPLIST'					=> 'Selecteer icoon voor gebruikerslijst',
	'ACP_POINTS_UPLIST_EXPLAIN'			=> 'Klik op naam om een nieuw Font Awesome icoon te selecteren.<br />Bekijk <samp><strong><a href="https://fontawesome.com/v4.7.0/icons/" title="Font Awesome">Font Awesome</a></strong></samp> voor meer info.',
	'ACP_POINTS_LOTTERYICON'			=> 'Selecteer icoon voor de lotto',
	'ACP_POINTS_LOTTERYICON_EXPLAIN'	=> 'Klik op naam om een nieuw Font Awesome icoon te selecteren.<br />Bekijk <samp><strong><a href="https://fontawesome.com/v4.7.0/icons/" title="Font Awesome">Font Awesome</a></strong></samp> voor meer info.',
	'ACP_POINTS_BANKICON'				=> 'Selecteer icoon voor de bank',
	'ACP_POINTS_BANKICON_EXPLAIN'		=> 'Klik op naam om een nieuw Font Awesome icoon te selecteren.<br />Bekijk <samp><strong><a href="https://fontawesome.com/v4.7.0/icons/" title="Font Awesome">Font Awesome</a></strong></samp> voor meer info.',

	'BANK_COST'							=> 'De kosten om een bank account te onderhouden',
	'BANK_COST_EXPLAIN'					=> 'Hier kan je de prijs instellen die gebruikers iedere periode dienen te betalen voor hun bank account. (Zet op 0 om deze functie uit te schakelen)',
	'BANK_ENABLE'						=> 'Schakel bank module in',
	'BANK_ENABLE_EXPLAIN'				=> 'Dit zal gebruikers toestaan de bank module te gebruiken',
	'BANK_FEES'							=> 'Opname kosten',
	'BANK_FEES_ERROR'					=> 'De afhaling kosten kunnen niet hoger zijn dan 100% !!',
	'BANK_FEES_EXPLAIN'					=> 'Het bedrag in procenten (%) dat gebruikers zullen moeten betalen wanneer ze afhalen van de bank',
	'BANK_INTEREST'						=> 'Interest rente',
	'BANK_INTERESTCUT'					=> 'Schakel interesten uit bij',
	'BANK_INTERESTCUTP'					=> '(kies 0 om deze functie uit te schakelen)',
	'BANK_INTERESTCUT_EXPLAIN'			=> 'Dit is het maximum bedrag waarop een gebruiker interesten zal ontvangen. Indien ze meer bezitten is de gekozen waarde het maximum! Zet op 0 op deze functie uit te schakelen.',
	'BANK_INTEREST_ERROR'				=> 'De interest rente kan niet hoger zijn dan 100% !!',
	'BANK_INTEREST_EXPLAIN'				=> 'Het bedrag in % van interesten',
	'BANK_MINDEPOSIT'					=> 'Min. storting',
	'BANK_MINDEPOSIT_EXPLAIN'			=> 'Het minimum bedrag dat gebruikers kunnen storten op de bank',
	'BANK_MINWITHDRAW'					=> 'Min. afhalen',
	'BANK_MINWITHDRAW_EXPLAIN'			=> 'Het maximum bedrag dat gebruikers kunnen afhalen van de bank',
	'BANK_NAME'							=> 'Naam van je bank',
	'BANK_NAME_EXPLAIN'					=> 'Vul een naam voor je bank in, Vb. Onze Forum Bank',
	'BANK_OPTIONS'						=> 'Bank Instellingen',
	'BANK_PAY'							=> 'Rente betalingstermijn',
	'BANK_PAY_EXPLAIN'					=> 'De periode tussen de bank betalingen',
	'BANK_TIME'							=> 'dagen',
	'BANK_VIEW'							=> 'Schakel punten bank in',
	'BANK_VIEW_EXPLAIN'					=> 'Dit zal de bank module inschakelen',

	'FORUM_OPTIONS'						=> 'Forum Punten',
	'FORUM_PEREDIT'						=> 'Punten per bewerking',
	'FORUM_PEREDIT_EXPLAIN'				=> 'Vul hier in hoeveel punten gebruikers zullen ontvangen voor het <strong>bewerken</strong> van een bericht. Houd er rekening mee dat ze ook extra punten zullen ontvangen die u hebt bepaald in de geavanceerde instellingen voor de punten.<br />Zet op 0 voor het ontvangen van punten uit te schakelen voor dit forum.',
	'FORUM_PERPOST'						=> 'Punten per bericht',
	'FORUM_PERPOST_EXPLAIN'				=> 'Vul hier in hoeveel punten gebruikers zullen ontvangen voor het plaatsen van <strong>berichten (antwoorden)</strong>. Houd er rekening mee dat ze ook extra punten zullen ontvangen die u hebt bepaald in de geavanceerde instellingen voor de punten.<br />Zet op 0 voor het ontvangen van punten uit te schakelen voor dit forum. Op deze manier zijn ook de geavanceerde punten instellingen uitgeschakeld voor dit forum!',
	'FORUM_PERTOPIC'					=> 'Punten per onderwerp',
	'FORUM_PERTOPIC_EXPLAIN'			=> 'Vul hier in hoeveel punten gebruikers zullen ontvangen voor het plaatsen van <strong>nieuwe onderwerp</strong>. Houd er rekening mee dat ze ook extra punten zullen ontvangen die u hebt bepaald in de geavanceerde instellingen voor de punten.<br />Zet op 0 voor het ontvangen van punten uit te schakelen voor dit forum. Op deze manier zijn ook de geavanceerde punten instellingen uitgeschakeld voor dit forum.',
	'FORUM_COST'						=> 'Punten per bijlage download',
	'FORUM_COST_EXPLAIN'				=> 'Vul hier in hoeveel punten gebruikers dienen te betalen om <strong>een bijlage te downloaden</strong>.<br />Zet op 0 op deze functie uit te schakelen.',
	'FORUM_COST_TOPIC'					=> 'Punten te betalen voor een nieuw onderwerp',
	'FORUM_COST_TOPIC_EXPLAIN'			=> 'Vul hier in hoeveel punten gebruikers dienen te betalen om een nieuw onderwerp te starten in dit forum',
	'FORUM_COST_POST'					=> 'Punten te betalen voor een nieuw bericht',
	'FORUM_COST_POST_EXPLAIN'			=> 'Vul hier in hoeveel punten gebruikers dienen te betalen voor het maken van een nieuw bericht in dit forum',
	'FORUM_POINT_SETTINGS'				=> 'Ultimate Points Instellingen',
	'FORUM_POINT_SETTINGS_EXPLAIN'		=> 'Hier kan je instellen hoeveel punten gebruikers zullen ontvangen voor het plaatsen van nieuwe onderwerpen, nieuwe berichten (antwoorden), en het bewerken van hun berichten. Deze instellingen zijn er op een per forum basis. Op deze manier kan je heel gedetailleerd instellen waar gebruikers punten zullen ontvangen, en waar niet.',
	'FORUM_POINT_SETTINGS_UPDATED'		=> 'Globale forum punten geupdate',
	'FORUM_POINT_UPDATE'				=> 'Update globale forum punten',
	'FORUM_POINT_UPDATE_CONFIRM'		=> '<br />Ben je zeker dat je alle forum punten wenst te updaten met de opgeven waarden?<br />Deze stap zal alle huidige instellingen overschrijven en kan niet ongedaan gemaakt worden!',

	'LOG_GROUP_TRANSFER_ADD'			=> 'Punten overgeschreven naar een groep',
	'LOG_GROUP_TRANSFER_SET'			=> 'Zet punten naar een nieuwe waarde voor een groep',
	'LOG_MOD_BANK'						=> '<strong>Wijzigde Bank Punten</strong><br />» %1$s',
	'LOG_MOD_POINTS'					=> '<strong>Wijzigde Punten</strong><br />» %1$s',
	'LOG_MOD_POINTS_BANK'				=> '<strong>Wijzigde Bank Instellingen</strong>',
	'LOG_MOD_POINTS_BANK_PAYS'			=> '<strong>Bank Rentebetalingen</strong><br />» %1$s',
	'LOG_MOD_POINTS_FORUM'				=> '<strong>Wijzigde Globale Forum Punten Instellingen</strong>',
	'LOG_MOD_POINTS_FORUM_SWITCH'		=> '<strong>Wijzigde Forum Punten Omschakelingen</strong>',
	'LOG_MOD_POINTS_FORUM_VALUES'		=> '<strong>Wijzigde Forum Punten Waardes</strong>',
	'LOG_MOD_POINTS_LOTTERY'			=> '<strong>Wijzigde Loterij Instellingen</strong>',
	'LOG_MOD_POINTS_RANDOM'				=> '<strong>Willekeurige punten gewonnen door</strong><br />» %1$s',
	'LOG_MOD_POINTS_ROBBERY'			=> '<strong>Wijzigde Overval Instellingen</strong>',
	'LOG_MOD_POINTS_SETTINGS'			=> '<strong>Wijzigde Punten Instellingen</strong>',
	'LOG_RESYNC_LOTTERY_HISTORY'		=> '<strong>De loterij geschiedenis reset war succesvol</strong>',
	'LOG_RESYNC_POINTSCOUNTS'			=> '<strong>Alle gebruikers punten werden succesvol gereset</strong>',
	'LOG_RESYNC_POINTSLOGSCOUNTS'		=> '<strong>Alle gebruiker logs werden succesvol gereset</strong>',
	'LOTTERY_BASE_AMOUNT'				=> 'Basis jackpot',
	'LOTTERY_BASE_AMOUNT_EXPLAIN'		=> 'De jackpot zal starten met dit bedrag. Indien er niemand gewonnen heeft zal het extra bedrag toegevoegd worden aan de volgende trekking. De jackpot zal niet verminderen als er iemand gewonnen heeft.',
	'LOTTERY_CHANCE'					=> 'Kansen om de jackpot te winnen',
	'LOTTERY_CHANCE_ERROR'				=> 'De kans om de jackpot te winnen kan niet hoger zijn dan 100% !!',
	'LOTTERY_CHANCE_EXPLAIN'			=> 'Hier kan je het percentage instellen om de jackpot te winnen. (Hoe hoger de waarde, hoe groter de kans om te winnen)',
	'LOTTERY_DISPLAY_STATS'				=> 'Toon de volgende trekking op de index',
	'LOTTERY_DISPLAY_STATS_EXPLAIN'		=> 'Dit zal de volgende loterij trekking tonen op de index pagina',
	'LOTTERY_DRAW_PERIOD'				=> 'Trekking periode',
	'LOTTERY_DRAW_PERIOD_EXPLAIN'		=> 'Aantal tijd in uren tussen iedere trekking. Dit wijzigen zal invloed hebben op de huidige trekking dag/tijd. Zet op 0 om de trekking uit te schakelen, de huidige tickets/jackpot zullen blijven.',
	'LOTTERY_DRAW_PERIOD_SHORT'			=> 'De trekking periode moet hoger zijn dan 0!',
	'LOTTERY_ENABLE'					=> 'Schakel loterij module in',
	'LOTTERY_ENABLE_EXPLAIN'			=> 'Dit zal gebruikers toestaan om de loterij module te gebruiken',
	'LOTTERY_MAX_TICKETS'				=> 'Max. aantal tickets',
	'LOTTERY_MAX_TICKETS_EXPLAIN'		=> 'Stel het maximum aantal tickets in dat een gebruiker kan kopen',
	'LOTTERY_MCHAT_OPTIONS'				=> 'Lotto mChat Instellingen',
	'LOTTERY_MCHAT_ENABLE'				=> 'Schakel berichten in mChat in voor de lotto',
	'LOTTERY_MCHAT_ENABLE_EXPLAIN'		=> 'Meld aangekochte tickets en jackpot winnaars in mChat.',
	'LOTTERY_MULTI_TICKETS'				=> 'Meerdere tickets toestaan',
	'LOTTERY_MULTI_TICKETS_EXPLAIN'		=> 'Stel dit in op "Ja" om gebruikers toe te staan meerdere tickets te kopen',
	'LOTTERY_NAME'						=> 'Naam van je loterij',
	'LOTTERY_NAME_EXPLAIN'				=> 'STel een naam in voor je loterij. Vb, Onze Forum Lotto',
	'LOTTERY_OPTIONS'					=> 'Loterij Instellingen',
	'LOTTERY_PM_ID'						=> 'Afzender ID',
	'LOTTERY_PM_ID_EXPLAIN'				=> 'Vul hier het gebruikers ID in van de gebruiker die als afzender moet gebruikt worden om de gelukkige winnaar te feliciteren. (0 = gebruik ID van de winnaar zelf)',
	'LOTTERY_TICKET_COST'				=> 'Ticket kosten',
	'LOTTERY_VIEW'						=> 'Schakel Punten Loterij in',
	'LOTTERY_VIEW_EXPLAIN'				=> 'Dit zal de loterij module inschakelen',

	'NO_RECIPIENT'						=> 'Geen ontvanger gedefinieerd.',

	'POINTS_ADV_OPTIONS'				=> 'Geavanceerde Punten Instellingen',
	'POINTS_ADV_OPTIONS_EXPLAIN'		=> 'Indien Forum Punten ingesteld zijn op 0 (uitgeschakeld), zullen alle instellingen hier niet berekent worden.',
	'POINTS_ATTACHMENT'					=> 'Algemene punten om een bijlage toe te voegen in een bericht',
	'POINTS_ATTACHMENT_PER_FILE'		=> 'Extra punten voor ieder bestand in een bijlage',
	'POINTS_BONUS_CHANCE'				=> 'Punten Bonus veranderen',
	'POINTS_BONUS_CHANCE_EXPLAIN'		=> 'De kans dat een gebruiker bonus punten ontvangt voor het maken van een nieuw onderwerp, bericht of bewerking.<br />Kansen zijn tussen 0 en 100%. U kunt decimalen gebruiken.<br />Zet op <strong>0</strong> om deze functie uit te schakelen.',
	'POINTS_BONUS_VALUE'				=> 'Bonus Punten waarde',
	'POINTS_BONUS_VALUE_EXPLAIN'		=> 'Geef grenzen waartussen we een willekeurige bonus bedrag zullen kiezen.<br />Indien je een vast bedrag wil zet je de minimum en maximum hetzelfde.',
	'POINTS_COMMENTS'					=> 'Commentaren toestaan',
	'POINTS_COMMENTS_EXPLAIN'			=> 'Sta gebruikers toe om commentaar achter te laten bij een overschrijving/donatie',
	'POINTS_CONFIG_SUCCESS'				=> 'De Ultimate Points instellingen werden succesvol geupdate',
	'POINTS_DISABLEMSG'					=> 'Uitgeschakeld bericht',
	'POINTS_DISABLEMSG_EXPLAIN'			=> 'Bericht om te tonen wanneer het Ultimate Points systeem is uitgeschakeld.',
	'POINTS_ENABLE'						=> 'Schakel punten in',
	'POINTS_ENABLE_EXPLAIN'				=> 'Sta gebruikers toe om Ultimate Points te gebruiken',
	'POINTS_GROUP_TRANSFER'				=> 'Groep Overschrijving',
	'POINTS_GROUP_TRANSFER_ADD'			=> 'Toevoegen',
	'POINTS_GROUP_TRANSFER_EXPLAIN'		=> 'Hier kan je waarden toevoegen, verminderen of instellen voor bepaalde groepen. Je kan ook een prive bericht zenden naar ieder lid van de groep. Handig als je berichten wenst te verzenden voor de feestdagen met een cadeau (je kan ook smileys en bbCodes gebruiken). Indien je geen prive bericht wenst te verzenden kan je het onderwerp en bericht veld gewoon leeg laten.',
	'POINTS_GROUP_TRANSFER_FUNCTION'	=> 'Functie',
	'POINTS_GROUP_TRANSFER_PM_COMMENT'	=> 'Commentaar voor je persoonlijke bericht',
	'POINTS_GROUP_TRANSFER_PM_ERROR'	=> 'Je moet een onderwerp <strong>EN</strong> de commentaar invullen indien je een persoonlijk bericht wenst te zenden tijdens de groep overschrijving.',
	'POINTS_GROUP_TRANSFER_PM_SUCCESS'	=> 'De groep overschrijving werd succesvol uitgevoerd en<br />de leden van de groep hebben je persoonlijke bericht ontvangen.',
	'POINTS_GROUP_TRANSFER_PM_TITLE'	=> 'Onderwerp voor het persoonlijke bericht',
	'POINTS_GROUP_TRANSFER_SEL_ERROR'	=> 'Je kan geen groep overschrijving doen naar de BOTS en GASTEN groep',
	'POINTS_GROUP_TRANSFER_SET'			=> 'Zet',
	'POINTS_GROUP_TRANSFER_SUBSTRACT'	=> 'Aftrekken',
	'POINTS_GROUP_TRANSFER_SUCCESS'		=> 'De groep overschrijving werd succesvol uitgevoerd.',
	'POINTS_GROUP_TRANSFER_USER'		=> 'Gebruikers groep',
	'POINTS_GROUP_TRANSFER_VALUE'		=> 'Waarde',

	'POINTS_ICON_PLACEHOLDER'			=> 'Klik om te selecteren',
	'POINTS_IMAGES_MEMBERLIST'			=> 'Toon een afbeelding in plaats van de punten naam in het profiel',
	'POINTS_IMAGES_MEMBERLIST_EXPLAIN'	=> 'Toon een afbeelding in plaats van de punten naam in het gebruikers profiel',
	'POINTS_IMAGES_TOPIC'				=> 'Toon een afbeelding in plaats van de punten',
	'POINTS_IMAGES_TOPIC_EXPLAIN'		=> 'Toon een afbeelding in berichten in plaats van de punten naam',
	'POINTS_LOGS'						=> 'Schakel Punten Logs in',
	'POINTS_LOGS_EXPLAIN'				=> 'Sta gebruiker toe de overschrijving logs te bekijken',
	'POINTS_MINIMUM'					=> '&nbsp;Minimum', // &nbsp; is for alignment of input boxes for Points Bonus Value
	'POINTS_MAXIMUM'					=> 'Maximum',
	'POINTS_NAME'						=> 'Punten naam',
	'POINTS_NAME_EXPLAIN'				=> 'De naam die je wenst te gebruiken in plaats van het woord <em>Punten</em> op je forum',
	'POINTS_NAME_UPLIST'				=> 'Naam gebruikerslijst',
	'POINTS_NAME_UPLIST_EXPLAIN'		=> 'De naam die u wilt weergeven in plaats van het woord <em>UP lijst</em> op uw forum.',
	'POINTS_POLL'						=> 'Punten per nieuwe poll',
	'POINTS_POLL_PER_OPTION'			=> 'Punten per optie in een poll',
	'POINTS_POST_PER_CHARACTER'			=> 'Punten per karakter in een nieuw bericht',
	'POINTS_POST_PER_WORD'				=> 'Punten per woord in een nieuw bericht',
	'POINTS_SHOW_PER_PAGE'				=> 'Aantal opgaven per pagina',
	'POINTS_SHOW_PER_PAGE_ERROR'		=> 'Het aantal opgaven per pagina om te tonen moet ten minste 5 zijn.',
	'POINTS_SHOW_PER_PAGE_EXPLAIN'		=> 'Vul hier het aantal opgaven in die getoond moeten worden per pagina in de logs en de loterij geschiedenis (min. 5)',
	'POINTS_SMILIES'					=> 'Smileys',
	'POINTS_STATS'						=> 'Toon Punten statistieken op de index',
	'POINTS_STATS_EXPLAIN'				=> 'Toon Punten statistieken op de hoofd forum index pagina',
	'POINTS_TOPIC_PER_CHARACTER'		=> 'Punten per karakter in nieuw onderwerp',
	'POINTS_TOPIC_PER_WORD'				=> 'Punten per woord in nieuw onderwerp',
	'POINTS_TRANSFER'					=> 'Overschrijvingen toestaan',
	'POINTS_TRANSFER_EXPLAIN'			=> 'Sta gebruikers toe om overschrijvingen/donaties naar elkaar te doen',
	'POINTS_TRANSFER_FEE'				=> 'Overschrijvingskosten',
	'POINTS_TRANSFER_FEE_EXPLAIN'		=> 'Percentage dat ingehouden wordt per overschrijving',
	'POINTS_TRANSFER_FEE_ERROR'			=> 'Overschrijvingskosten kunnen geen 100% of meer zijn.',
	'POINTS_TRANSFER_PM'				=> 'Informeer gebruiker via PB van een overschrijving',
	'POINTS_TRANSFER_PM_EXPLAIN'		=> 'Sta gebruikers toe om een PB notificatie te ontvangen wanneer iemand punten naar ze stuurt.',
	'POINTS_WARN'						=> 'Aantal punten om af te trekken per waarschuwing (Zet op 0 om deze functie uit te schakelen)',

	'REG_POINTS_BONUS'					=> 'Registratie Punten Bonus',
	'RESYNC_ATTENTION'					=> 'De volgende acties kunnen niet ongedaan gemaakt worden!!',
	'RESYNC_DESC'						=> 'Reset Gebruikers Punten en Logs',
	'RESYNC_LOTTERY_HISTORY'			=> 'Reset de loterij geschiedenis',
	'RESYNC_LOTTERY_HISTORY_CONFIRM'	=> 'Ben je zeker dat je de loterij geschiedenis wenst te resetten?<br />Opgelet: Deze actie kan niet ongedaan gemaakt worden!',
	'RESYNC_LOTTERY_HISTORY_EXPLAIN'	=> 'Dit zal de volledige loterij geschiedenis resetten',
	'RESYNC_POINTS'						=> 'Reset gebruikers Punten',
	'RESYNC_POINTSLOGS'					=> 'Reset gebruikers Logs',
	'RESYNC_POINTSLOGS_CONFIRM'			=> 'Ben je zeker dat je de gebruikers logs wenst te resetten?<br />Opgelet: Deze actie kan niet ongedaan gemaakt worden!',
	'RESYNC_POINTSLOGS_EXPLAIN'			=> 'Verwijder alle gebruikers logs',
	'RESYNC_POINTS_CONFIRM'				=> 'Ben je zeker dat je alle gebruikers Punten wenst te resetten?<br />Opgelet: Deze actie kan niet ongedaan gemaakt worden!',
	'RESYNC_POINTS_EXPLAIN'				=> 'Reset alle gebruikers Punten naar 0',
	'ROBBERY_CHANCE'					=> 'Kans op een succesvolle overval',
	'ROBBERY_CHANCE_ERROR'				=> 'De kans op een succesvolle overval kan niet hoger zijn dan 100% !!',
	'ROBBERY_CHANCE_EXPLAIN'			=> 'Hier kan je het percentage instellen om een succesvolle overval te doen. (Hoe hoger de waarde, hoe groter de kans op succes)',
	'ROBBERY_CHANCE_MINIMUM'			=> 'De kans op een succesvolle overval moet hoger zijn dan 0% !!',
	'ROBBERY_ENABLE'					=> 'Schakel Overval Module in',
	'ROBBERY_ENABLE_EXPLAIN'			=> 'Dit zal gebruikers toestaan om de Overval Module te gebruiken',
	'ROBBERY_LOOSE'						=> 'Straf voor een mislukte overval',
	'ROBBERY_LOOSE_ERROR'				=> 'De straf voor een mislukte overval kan niet hoger zijn dan 100% !!',
	'ROBBERY_LOOSE_EXPLAIN'				=> 'Indien een overval mislukt zal de gebruiker die iemand probeerde te overvallen x% verliezen van de verwachte overval waarde',
	'ROBBERY_LOOSE_MINIMUM'				=> 'De straf voor een mislukte overval zou geen 0% mogen zijn. Je moet de dief toch wel een straf geven !!',
	'ROBBERY_MAX_ROB'					=> 'Maximum overval percentage',
	'ROBBERY_MAX_ROB_ERROR'				=> 'Je kan geen waarde instellen hoger dan 100% !!',
	'ROBBERY_MAX_ROB_EXPLAIN'			=> 'Deze waarde is het percentage van het gebruikers cash bedrag dat in 1 keer gestolen kan worden',
	'ROBBERY_MAX_ROB_MINIMUM'			=> 'De waarde van de maximum overval waarde moet hoger zijn dan 0%. Anders heeft deze optie niet veel nut!',
	'ROBBERY_MCHAT_OPTIONS'				=> 'Overval mChat instellingen',
	'ROBBERY_MCHAT_ENABLE'				=> 'Schakel berichten in mChat in voor overvallen',
	'ROBBERY_MCHAT_ENABLE_EXPLAIN'		=> 'Meld mislukte en succesvolle overvallen.',
	'ROBBERY_NOTIFY'					=> 'Zend een notificatie naar het slachtoffer',
	'ROBBERY_NOTIFY_EXPLAIN'			=> 'Dit dal de optie activeren om een notificatie te verzenden naar het slachtoffer',
	'ROBBERY_OPTIONS'					=> 'Overval Instellingen',

	'TOP_POINTS'						=> 'Aantal van de top rijkste leden om te tonen',
	'TOP_POINTS_EXPLAIN'				=> 'Hier kan je de waarde instellen van de top rijkste leden om te tonen. Werkt in verschillende weergaven',
	'TRANSFER_MCHAT_ENABLE'				=> 'Schakel berichten in mChat in voor overschrijvingen',
	'TRANSFER_MCHAT_ENABLE_EXPLAIN'		=> 'Post overschrijvingen van gebruikers.',
	'TRANSFER_MCHAT_OPTIONS'			=> 'Overschrijving mChat instellingen',

	'UPLIST_ENABLE'						=> 'Schakel Ultimate Points Lijst in',
	'UPLIST_ENABLE_EXPLAIN'				=> 'Sta gebruikers toe om de Ultimate Points lijst te gebruiken',
	'USER_POINTS'						=> 'Gebruikers Punten',
	'USER_POINTS_EXPLAIN'				=> 'Aantal punten die de gebruiker bezit',
));
