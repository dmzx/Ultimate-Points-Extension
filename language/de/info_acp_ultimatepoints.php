<?php
/**
*
* @version $Id: info_acp_ultimatepoints.php 160 2018-06-01 12:08:34Z Scanialady $
* @package phpBB Extension - Ultimate Points (Deutsch)
* @copyright (c) 2015 dmzx & posey - https://www.dmzx-web.net
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
// ‚ ‘ ’ « » „ “ ” …
//

// Merge the following language entries into the lang array
$lang = array_merge($lang, array(
	'ACP_POINTS'						=> 'Ultimate Points',
	'ACP_POINTS_BANK_EXPLAIN'			=> 'Hier kannst du die Einstellungen für das Bank Modul vornehmen.',
	'ACP_POINTS_BANK_TITLE'				=> 'Bank Einstellungen',
	'ACP_POINTS_DEACTIVATED'			=> 'Der Ultimate Points ist derzeit deaktiviert!',
	'ACP_POINTS_FORUM_EXPLAIN'			=> 'Hier kannst du die Standartwerte für die Forenpunkte und den Schalter für Dateianhang Kosten aller Foren auf einmal einstellen.<br />Diese Einstellungen gelten dann für <strong>ALLE</strong> Foren. Bitte beachte: Wenn du zuvor einzelne Foren manuell verändert hast, werden diese Einstellungen überschrieben und du musst sie erneut vornehmen!',
	'ACP_POINTS_FORUM_TITLE'			=> 'Einstellungen Forenpunkte',
	'ACP_POINTS_INDEX_EXPLAIN'			=> 'Hier kannst du die allgemeinen Einstellungen für Ultimate Points ändern.',
	'ACP_POINTS_INDEX_TITLE'			=> 'Einstellungen Punkte',
	'ACP_POINTS_LOTTERY_EXPLAIN'		=> 'Hier kannst du die Einstellungen des Lotteriemoduls ändern',
	'ACP_POINTS_LOTTERY_TITLE'			=> 'Einstellungen Lotterie',
	'ACP_POINTS_ROBBERY_EXPLAIN'		=> 'Hier kannst du die Einstellungen des Diebstahlmoduls ändern.',
	'ACP_POINTS_ROBBERY_TITLE'			=> 'Einstellungen Diebstahl',
	'ACP_POINTS_VALUES_HINT'			=> '<strong>Hinweis: </strong>Gib Werte immer ohne ein Tausender-Trennzeichen ein<br>und Dezimalwerte mit einem Punkt, z.B. 1000.50',
	'ACP_USER_POINTS_TITLE'				=> 'Ultimate Points Einstellungen',
	'ACP_POINTS_MAINICON'				=> 'Wähle Haupt-Icon',
	'ACP_POINTS_MAINICON_EXPLAIN'		=> 'Klicke auf den Namen, um ein neues Font Awesome-Icon zu wählen.<br />Siehe <samp><strong><a href="https://fontawesome.com/v4.7.0/icons/" title="Font Awesome">Font Awesome</a></strong></samp> für weitere Informationen.',
	'ACP_POINTS_UPLIST'					=> 'Wähle Icon für Benutzerliste',
	'ACP_POINTS_UPLIST_EXPLAIN'			=> 'Klicke auf den Namen, um ein neues Font Awesome-Icon zu wählen.<br />Siehe <samp><strong><a href="https://fontawesome.com/v4.7.0/icons/" title="Font Awesome">Font Awesome</a></strong></samp> für mehr Informationen.',
	'ACP_POINTS_LOTTERYICON'			=> 'Wähle Icon für Lotterie',
	'ACP_POINTS_LOTTERYICON_EXPLAIN'	=> 'Klicke auf den Namen, um ein neues Font Awesome-Icon zu wählen.<br />Siehe <samp><strong><a href="https://fontawesome.com/v4.7.0/icons/" title="Font Awesome">Font Awesome</a></strong></samp> für weitere Informationen.',
	'ACP_POINTS_BANKICON'				=> 'Wähle Icon für Bank',
	'ACP_POINTS_BANKICON_EXPLAIN'		=> 'Klicke auf den Namen um ein neues Font Awesome-Icon zu wählen.<br />Siehe <samp><strong><a href="https://fontawesome.com/v4.7.0/icons/" title="Font Awesome">Font Awesome</a></strong></samp> für weitere Informationen.',

	'BANK_COST'							=> 'Die Kosten für die Verwaltung eines Bankkontos',
	'BANK_COST_EXPLAIN'					=> 'Hier stellst du den Preis ein, den Benutzer für ihr Bankkonto je Periode zu bezahlen haben (setze 0 um die Funktion zu deaktivieren).',
	'BANK_ENABLE'						=> 'Aktiviere Bankmodul',
	'BANK_ENABLE_EXPLAIN'				=> 'Dies erlaubt den Benutzern, das Bankmodul zu verwenden.',
	'BANK_FEES'							=> 'Auszahlungsgebühr',
	'BANK_FEES_ERROR'					=> 'Die Auszahlungsgebühr kann nicht höher als 100\% sein!',
	'BANK_FEES_EXPLAIN'					=> 'Der Betrag in Prozent (\%), den Benutzer zahlen müssen, wenn sie etwas von der Bank abheben.',
	'BANK_INTEREST'						=> 'Zinssatz',
	'BANK_INTERESTCUT'					=> 'Deaktiviere Zinssatz ab',
	'BANK_INTERESTCUTP'					=> '(setze 0 um diese Funktion zu deaktivieren)',
	'BANK_INTERESTCUT_EXPLAIN'			=> 'Das ist der maximale Betrag, für den ein Benutzer Zinsen erhält. Wenn sie mehr besitzen, ist der gesetzte Betrag trotzdem das Maximum! Setze 0 um diese Funktion zu deaktivieren.',
	'BANK_INTEREST_ERROR'				=> 'Der Zinssatz kann nicht höher sein als 100\%!',
	'BANK_INTEREST_EXPLAIN'				=> 'Der Zinssatz in \%',
	'BANK_MINDEPOSIT'					=> 'Min. Einzahlung',
	'BANK_MINDEPOSIT_EXPLAIN'			=> 'Der Mindestbetrag, den ein Benutzer auf sein Bankkonto einzahlen kann.',
	'BANK_MINWITHDRAW'					=> 'Min. Auszahlung',
	'BANK_MINWITHDRAW_EXPLAIN'			=> 'Der Mindestbetrag, den ein Benutzer von seinem Bankkonto auszahlen lassen kann',
	'BANK_NAME'							=> 'Name deiner Bank',
	'BANK_NAME_EXPLAIN'					=> 'Gib einen Namen für deine Bank ein, z.B. Unsere Forum Bank',
	'BANK_OPTIONS'						=> 'Einstellungen Bank',
	'BANK_PAY'							=> 'Zeitrum Zinszahlung',
	'BANK_PAY_EXPLAIN'					=> 'Die Zeitperiode zwischen den Bankzahlungen',
	'BANK_TIME'							=> 'Tage',
	'BANK_VIEW'							=> 'Aktiviere Bank',
	'BANK_VIEW_EXPLAIN'					=> 'Dies aktiviert das Bankmodul',

	'FORUM_OPTIONS'						=> 'Forumpunkte',
	'FORUM_PEREDIT'						=> 'Punkte je Beitragsbearbeitung',
	'FORUM_PEREDIT_EXPLAIN'				=> 'Hier kannst du einstellen, wieviele Punkte ein Benutzer für eine <strong>Änderung</strong> seines Beitrages erhalten soll. Setze hier 0, wenn die Benutzer keine Punkte in diesem Forum bekommen sollen.',
	'FORUM_PERPOST'						=> 'Punkte pro Beitrag',
	'FORUM_PERPOST_EXPLAIN'				=> 'Hier kannst du einstellen, wieviele Punkte ein Benutzer für das Schreiben eines <strong>Beitrages (Antwort)</strong> erhalten soll. Bitte beachte, daß der Benutzer auch die zusätzlich Punkte erhält, die du in den Erweiterten Punkteeinstellung vorgenommen hast.<br /> Setze hier 0, wenn die Benutzer keine Punkte bekommen sollen. Damit wird auch gleichzeite die Vergabe der erweiterten Punkt ausgeschaltet!',
	'FORUM_PERTOPIC'					=> 'Punkte pro Thema',
	'FORUM_PERTOPIC_EXPLAIN'			=> 'Hier kannst du einstellen, wieviele Punkte ein Benutzer für das Schreiben eines <strong>neuen Themas</strong> erhalten soll. Bitte beachte, daß der Benutzer auch die zusätzlich Punkte erhält, die du in den Erweiterten Punkteeinstellung vorgenommen hast.<br /> Setze hier 0, wenn die Benutzer keine Punkte bekommen sollen. Damit wird auch gleichzeite die Vergabe der erweiterten Punkt ausgeschaltet!',
	'FORUM_COST'						=> 'Punktekosten je Anhang-Download',
	'FORUM_COST_EXPLAIN'				=> 'Hier kannst du einstellen, wie viele Punkte ein Benutzer für den <strong>Download eines Dateianhangs</strong> zahlen muss.<br />Setze hier 0 um die Funktion zu deaktivieren.',
	'FORUM_COST_TOPIC'					=> 'Punktekosten für neue Themen',
	'FORUM_COST_TOPIC_EXPLAIN'			=> 'Hier kannst du einstellen, wie viele Punkte ein Benutzer für die <strong>Erstellung eines neuen Themas</strong> in diesem Forum zahlen muss.',
	'FORUM_COST_POST'					=> 'Punktekosten für neue Beiträge',
	'FORUM_COST_POST_EXPLAIN'			=> 'Hier kannst du einstellen, wie viele Punkte ein Benutzer für die <strong>Erstellung eines neuen Beitrags</strong> in diesem Forum zahlen muss.',
	'FORUM_POINT_SETTINGS'				=> 'Ultimate Points Einstellungen',
	'FORUM_POINT_SETTINGS_EXPLAIN'		=> 'Hier kannst du einstellen, wie viele Punkte Benutzer erhalten werden für die Erstellung neuer Themen, neuer Beiträge (Antworten) und die Bearbeitung ihrer Beiträge. Diese Einstellungen wirken auf einer pro-Forum-Basis. Auf diese Weise kannst du sehr detailliert bestimmen, wo Benutzer Punkte erhalten und wo nicht.',
	'FORUM_POINT_SETTINGS_UPDATED'		=> 'Globale Forenpunkte aktualisiert',
	'FORUM_POINT_UPDATE'				=> 'Aktualisiere globale Forenpunkte',
	'FORUM_POINT_UPDATE_CONFIRM'		=> '<br />Bist du sicher, dass du alle Forenpunkte mit den eingetragenen Werten aktualisieren willst? <br />Dieser Schritt wird alle derzeitigen Einstellungen überschreiben und ist nicht rückgängig zu machen!',

	'LOG_GROUP_TRANSFER_ADD'			=> 'Einer Gruppe wurden Punkte überwiesen',
	'LOG_GROUP_TRANSFER_SET'			=> 'Die Punkte einer Gruppe wurden auf einen neuen Wert gesetzt',
	'LOG_MOD_BANK'						=> '<strong>Bankpunkte bearbeitet</strong><br />» %1$s',
	'LOG_MOD_POINTS'					=> '<strong>Punkte bearbeitet</strong><br />» %1$s',
	'LOG_MOD_POINTS_BANK'				=> '<strong>Bankeinstellungen bearbeitet</strong>',
	'LOG_MOD_POINTS_BANK_PAYS'			=> '<strong>Zinszahlungen Bank</strong><br />» %1$s',
	'LOG_MOD_POINTS_FORUM'				=> '<strong>Globale Forenpunkte-Einstellungen bearbeitet</strong>',
	'LOG_MOD_POINTS_FORUM_SWITCH'		=> '<strong>Forenpunkte-Schalter bearbeitet</strong>',
	'LOG_MOD_POINTS_FORUM_VALUES'		=> '<strong>Forenpunkte-Werte bearbeitet</strong>',
	'LOG_MOD_POINTS_LOTTERY'			=> '<strong>Lotterieeinstellungen bearbeitet</strong>',
	'LOG_MOD_POINTS_RANDOM'				=> '<strong>Zufällige Punkte gewonnen von</strong><br />» %1$s',
	'LOG_MOD_POINTS_ROBBERY'			=> '<strong>Diebstahleinstellungen bearbeitet</strong>',
	'LOG_MOD_POINTS_SETTINGS'			=> '<strong>Punkteeinstellungen bearbeitet</strong>',
	'LOG_RESYNC_LOTTERY_HISTORY'		=> '<strong>Lotterieverlauf erfolgreich zurückgesetzt</strong>',
	'LOG_RESYNC_POINTSCOUNTS'			=> '<strong>Alle Benutzerpunkte wurden erfolgreich zurückgesetzt</strong>',
	'LOG_RESYNC_POINTSLOGSCOUNTS'		=> '<strong>Alle Benutzerprotokolle wurden erfolgreich zurückgesetzt</strong>',
	'LOTTERY_BASE_AMOUNT'				=> 'Basis-Jackpot',
	'LOTTERY_BASE_AMOUNT_EXPLAIN'		=> 'Der Jackpot beginnt mit diesem Wert. Während der Kaufphase kann dieser Wert ansteigen. Alles was zusätzlich eingezahlt wird, erhöht den Gesamt-Jackpot für die folgende Auslosung. Der Jackpot kann nicht unterhalb dieses Wertes fallen.',
	'LOTTERY_CHANCE'					=> 'Wahrscheinlichkeit, den Jackpot zu gewinnen',
	'LOTTERY_CHANCE_ERROR'				=> 'Die Chance zu gewinnen kann nicht höher sein als 100\% !!',
	'LOTTERY_CHANCE_EXPLAIN'			=> 'Setze hier die Wahrscheinlichkeit für einen erfolgreichen Gewinn (je größer die Zahl, desto höher ist die Wahrscheinlichkeit den Jackpot zu gewinnen).',
	'LOTTERY_DISPLAY_STATS'				=> 'Nächste Ziehung auf der Hauptseite anzeigen',
	'LOTTERY_DISPLAY_STATS_EXPLAIN'		=> 'Dies wird den Zeitpunkt der nächsten Ziehung für die Lotterie auf der Hauptseite des Forums anzeigen.',
	'LOTTERY_DRAW_PERIOD'				=> 'Kaufphase',
	'LOTTERY_DRAW_PERIOD_EXPLAIN'		=> 'Zeitraum in Stunden zwischen den Auslosungen. Eine Änderung hier wirkt sich direkt auf die aktuelle Ziehung (Tag/Zeit) aus. Setze auf 0 um die Auslosung auszusetzen, die aktuellen Lose und der Jackpot bleiben erhalten.',
	'LOTTERY_DRAW_PERIOD_SHORT'			=> 'Die Kaufphase muss höher sein als 0!',
	'LOTTERY_ENABLE'					=> 'Aktiviere Lotteriemodul',
	'LOTTERY_ENABLE_EXPLAIN'			=> 'Dies wird den Benutzern erlauben, das Lotteriemodul zu verwenden.',
	'LOTTERY_MAX_TICKETS'				=> 'Maximale Anzahl Lose',
	'LOTTERY_MAX_TICKETS_EXPLAIN'		=> 'Hier kannst du die maximale Anzahl an Losen einstellen, die ein Benutzer kaufen kann.',
	'LOTTERY_MCHAT_OPTIONS'				=> 'Lotterie-Einstellungen für mChat',
	'LOTTERY_MCHAT_ENABLE'				=> 'Aktiviere mChat-Beitrag für Lotterie',
	'LOTTERY_MCHAT_ENABLE_EXPLAIN'		=> 'Sende gekaufte Lose und Jackpot-Gewinner in mChat.',
	'LOTTERY_MULTI_TICKETS'				=> 'Erlaube Kauf mehrerer Lose',
	'LOTTERY_MULTI_TICKETS_EXPLAIN'		=> 'Setze dies auf "JA" um Benutzern zu erlauben, mehr als ein Los zu kaufen.',
	'LOTTERY_NAME'						=> 'Name deiner Lotterie',
	'LOTTERY_NAME_EXPLAIN'				=> 'Gib einen Namen für deine Lotterie ein, z.B. Unsere Forumlotterie.',
	'LOTTERY_OPTIONS'					=> 'Lotterieeinstellungen',
	'LOTTERY_PM_ID'						=> 'Absender ID',
	'LOTTERY_PM_ID_EXPLAIN'				=> 'Gib hier die Benutzer-ID des Benutzers ein, von dem die PN an den Gewinner gesendet wird (0 = Die ID des Gewinners).',
	'LOTTERY_TICKET_COST'				=> 'Kosten für ein Los',
	'LOTTERY_VIEW'						=> 'Aktiviere Lotteriemodul',
	'LOTTERY_VIEW_EXPLAIN'				=> 'Dies wird das Lotteriemodul im Punkteblock aktivieren.',

	'NO_RECIPIENT'						=> 'Kein Empfänger angegeben.',

	'POINTS_ADV_OPTIONS'				=> 'Erweiterte Punkteeinstellungen',
	'POINTS_ADV_OPTIONS_EXPLAIN'		=> 'Wenn die Forenpunkte auf 0 gesetzt sind (deaktiviert), haben alle Einstellungen hier keine Wirkung.',
	'POINTS_ATTACHMENT'					=> 'Allgemeine Punkte für hinzufügen von Dateianhängen im Beitrag',
	'POINTS_ATTACHMENT_PER_FILE'		=> 'Zusätzliche Punkte für jeden Dateianhang',
	'POINTS_BONUS_CHANCE'				=> 'Chance auf Punktebonus',
	'POINTS_BONUS_CHANCE_EXPLAIN'		=> 'Die Chance, dass ein Benutzer Bonuspunkte für die Erstellung eines neuen Themas, eines Beitrags oder einer Beitragsbearbeitung erhält. <br />Die Chance liegt zwischen 0 and 100%, du kannst Dezimalstellen benutzen.<br />Setze auf <strong>0</strong> um die Funktion zu deaktivieren.',
	'POINTS_BONUS_VALUE'				=> 'Wert Punktebonus',
	'POINTS_BONUS_VALUE_EXPLAIN'		=> 'Gib hier die Grenzen ein, zwischen denen ein zufälliger Bonus gewählt werden soll. <br />Wenn du einen festen Betrag möchtest, setze Minimum und Maximum auf den gleichen Betrag.',
	'POINTS_COMMENTS'					=> 'Erlaube Kommentare',
	'POINTS_COMMENTS_EXPLAIN'			=> 'Erlaubt den Benutzer ihrer Überweisung einen Kommentar hinzuzufügen.',
	'POINTS_CONFIG_SUCCESS'				=> 'Du hast die Punkte Einstellung erfolgreich aktualisiert',
	'POINTS_DISABLEMSG'					=> 'Deaktivierungsnachricht',
	'POINTS_DISABLEMSG_EXPLAIN'			=> 'Nachricht, die angezeigt wird, wenn der Ulitmate Points deaktiviert ist.',
	'POINTS_ENABLE'						=> 'Punkte aktivieren',
	'POINTS_ENABLE_EXPLAIN'				=> 'Erlaubt den Benutzern, Ultimate Points zu verwenden.',
	'POINTS_GROUP_TRANSFER'				=> 'Gruppenüberweisung',
	'POINTS_GROUP_TRANSFER_ADD'			=> 'Hinzufügen',
	'POINTS_GROUP_TRANSFER_EXPLAIN'		=> 'Hier kannst du Punkte an eine Gruppe überweisen, abziehen oder deren Punktestand auf einen gleichen Wert setzen. Du kannst zu deiner Überweisung den Benutzern auch eine persönliche Nachricht mit dem Grund für den Transfer zukommen lassen (du kannst Smilies und BBCodes verwenden). Wenn du keine persönliche Nachricht senden willst, lasse die Felder Überschrift und Kommentar einfach leer.',
	'POINTS_GROUP_TRANSFER_FUNCTION'	=> 'Funktion',
	'POINTS_GROUP_TRANSFER_PM_COMMENT'	=> 'Kommentar für die persönliche Nachricht',
	'POINTS_GROUP_TRANSFER_PM_ERROR'	=> 'Um eine persönlich Nachricht zum Gruppentransfer zu senden, musst du die Felder Überschrift <strong>und</strong> Kommentar ausfüllen!',
	'POINTS_GROUP_TRANSFER_PM_SUCCESS'	=> 'Der Gruppentransfer wurde erfolgreich abgeschlossen und<br />die Benutzer der Gruppe haben deine persönliche Nachricht erhalten',
	'POINTS_GROUP_TRANSFER_PM_TITLE'	=> 'Überschrift für die persönliche Nachricht',
	'POINTS_GROUP_TRANSFER_SEL_ERROR'	=> 'Du kannst keine Gruppenüberweisung an die Gruppen Bots und Gäste durchführen!',
	'POINTS_GROUP_TRANSFER_SET'			=> 'Setzen',
	'POINTS_GROUP_TRANSFER_SUBSTRACT'	=> 'Abziehen',
	'POINTS_GROUP_TRANSFER_SUCCESS'		=> 'Der Gruppentransfer wurde erfolgreich abgeschlossen.',
	'POINTS_GROUP_TRANSFER_USER'		=> 'Benutzergruppe',
	'POINTS_GROUP_TRANSFER_VALUE'		=> 'Betrag',
	'POINTS_ICON_PLACEHOLDER'			=> 'Klicke für Auswahl',
	'POINTS_IMAGES_MEMBERLIST'			=> 'Zeige ein Bild hinter den Punkten im Profil',
	'POINTS_IMAGES_MEMBERLIST_EXPLAIN'	=> 'Zeige im Profil des Benutzers ein Bild hinter den Punkten.',
	'POINTS_IMAGES_TOPIC'				=> 'Zeige ein Bild hinter den Punkten',
	'POINTS_IMAGES_TOPIC_EXPLAIN'		=> 'Zeige in Beiträgen ein Bild hinter den Punkten.',
	'POINTS_LOGS'						=> 'Punkte Log aktivieren',
	'POINTS_LOGS_EXPLAIN'				=> 'Erlaube den Benutzern ein Protokoll der Transaktionen zu sehen (Kontobewegeungen von sich und an sich).',
	'POINTS_MINIMUM'					=> '&nbsp;Minimum', // &nbsp; is for alignment of input boxes for Points Bonus Value
	'POINTS_MAXIMUM'					=> 'Maximum',
	'POINTS_NAME'						=> 'Punkte',
	'POINTS_NAME_EXPLAIN'				=> 'Der Anzeigenamen, den du statt des Wortes <em>Punkte</em> im Forum verwenden möchtest.',
	'POINTS_NAME_UPLIST'				=> 'Name Benutzerliste',
	'POINTS_NAME_UPLIST_EXPLAIN'		=> 'Der Name, den du statt <em>UP Liste</em> auf deinem Board anzeigen möchtest.',
	'POINTS_POLL'						=> 'Punkte für neue Umfragen',
	'POINTS_POLL_PER_OPTION'			=> 'Punkte für jede Auswahl in neuen Umfragen',
	'POINTS_POST_PER_CHARACTER'			=> 'Punkte pro Zeichen in neuen Beiträgen',
	'POINTS_POST_PER_WORD'				=> 'Punkte pro Wort in neuen Beiträgen',
	'POINTS_SHOW_PER_PAGE'				=> 'Anzeige Einträge pro Seite',
	'POINTS_SHOW_PER_PAGE_ERROR'		=> 'Die Anzahl der anzuzeigenden Einträge muss mind. 5 betragen. Ansonsten haut es dir dein Layout durcheinander!',
	'POINTS_SHOW_PER_PAGE_EXPLAIN'		=> 'Gib hier an, wieviele Eintrage in den Logs und dem Lotterieverlauf pro Seite angezeigt werden sollen (min. 5)',
	'POINTS_SMILIES'					=> 'Smilies',
	'POINTS_STATS'						=> 'Zeige Punkte Statistik im Index',
	'POINTS_STATS_EXPLAIN'				=> 'Zeige Punkte Statistik auf der Hauptseite.',
	'POINTS_TOPIC_PER_CHARACTER'		=> 'Punkte pro Zeichen in neuen Themen',
	'POINTS_TOPIC_PER_WORD'				=> 'Punkte pro Wort in neuen Themen',
	'POINTS_TRANSFER'					=> 'Überweisungen erlauben',
	'POINTS_TRANSFER_EXPLAIN'			=> 'Erlaube den Benutzern untereinander Punkte zu überweisen.',
	'POINTS_TRANSFER_FEE'				=> 'Überweisungsgebühren',
	'POINTS_TRANSFER_FEE_EXPLAIN'		=> 'Prozentualer Betrag, der von einer Überweisung einbehalten wird.',
	'POINTS_TRANSFER_FEE_ERROR'			=> 'Überweisungsgebühren können nicht 100\% oder mehr ausmachen!.',
	'POINTS_TRANSFER_PM'				=> 'Benachrichtige Benutzer über Überweisung mit PN',
	'POINTS_TRANSFER_PM_EXPLAIN'		=> 'Erlaube Benutzern eine Mitteilung über PN zu erhalten, wenn jemand ihnen Punkte gesendet hat.',
	'POINTS_WARN'						=> 'Betrag an Punkten, die einem Benutzer abgezogen werden, wenn er verwarnt wird (setze 0 um diese Funktion zu deaktivieren)',

	'REG_POINTS_BONUS'					=> 'Punktebonus für Registrierung',
	'RESYNC_ATTENTION'					=> 'Die folgenden Aktionen können nicht rückgängig gemacht werden!!',
	'RESYNC_DESC'						=> 'Benutzerpunkte und Protokolle zurücksetzen',
	'RESYNC_LOTTERY_HISTORY'			=> 'Lotterieverlauf zurücksetzen',
	'RESYNC_LOTTERY_HISTORY_CONFIRM'	=> 'Bist du sicher, dass du den Lotterieverlauf zurücksetzen möchtest?<br />Hinweis: Diese Aktion kann nicht rückgängig gemacht werden!',
	'RESYNC_LOTTERY_HISTORY_EXPLAIN'	=> 'Dies wird den kompletten Lotterieverlauf zurücksetzen',
	'RESYNC_POINTS'						=> 'Benutzerpunkte zurücksetzen',
	'RESYNC_POINTSLOGS'					=> 'Benutzerprotokolle zurücksetzen',
	'RESYNC_POINTSLOGS_CONFIRM'			=> 'Bist du sicher, dass du die Benutzerprotokolle zurücksetzen möchtest?<br /><br />Hinweis: Diese Aktion kann nicht rückgängig gemacht werden!',
	'RESYNC_POINTSLOGS_EXPLAIN'			=> 'Lösche alle Benutzerprotokolle',
	'RESYNC_POINTS_CONFIRM'				=> 'Bist du sicher, dass du alle Benutzerpunkte zurücksetzen möchtest?<br />Hinweis: Diese Aktion kann nicht rückgängig gemacht werden!',
	'RESYNC_POINTS_EXPLAIN'				=> 'Setze alle Benutzerpunktekonten zurück auf Null',
	'ROBBERY_CHANCE'					=> 'Chance für einen erfolgreichen Diebstahl',
	'ROBBERY_CHANCE_ERROR'				=> 'Die Chance auf einen erfolgreichen Diebstahl kann nicht höher sein als 100% !!',
	'ROBBERY_CHANCE_EXPLAIN'			=> 'Hier kannst du den Prozentsatz für einen erfolgreichen Diebstahl eingeben (je höher der Wert, umso größer die Chance auf Erfolg)',
	'ROBBERY_CHANCE_MINIMUM'			=> 'Die Chance auf einen erfolgreichen Diebstahl muss höher sein als 0% !',
	'ROBBERY_ENABLE'					=> 'Aktiviere Diebstahlmodul',
	'ROBBERY_ENABLE_EXPLAIN'			=> 'Dies wird den Benutzern erlauben, das Diebstahlmodul zu benutzen',
	'ROBBERY_LOOSE'						=> 'Strafe für einen fehlgeschlagenen Diebstahl',
	'ROBBERY_LOOSE_ERROR'				=> 'Die Strafe für einen fehlgeschlagenen Diebstahl kann nicht höher als 100% sein!!',
	'ROBBERY_LOOSE_EXPLAIN'				=> 'Falls der Diebstahl fehlschlägt, verliert der verhinderte Dieb x% des versuchten Diebstahls',
	'ROBBERY_LOOSE_MINIMUM'				=> 'Der Wert für Strafe sollte schon etwas größer als 0% sein. Der Dieb sollte schon bestraft werden!',
	'ROBBERY_MAX_ROB'					=> 'Prozentsatz des maximalen Diebstahls',
	'ROBBERY_MAX_ROB_ERROR'				=> 'Du kannst keinen Wert höher als 100\% einstellen!',
	'ROBBERY_MAX_ROB_EXPLAIN'			=> 'Dieser Wert gibt an, wieviel Prozent des Barvermögens eines Benutzers man maximal stehlen kann',
	'ROBBERY_MAX_ROB_MINIMUM'			=> 'Der Wert für die Höhe des Diebstahls sollte schon etwas höher als 0% sein. Sonst macht diese Option ja keinen Sinn!',
	'ROBBERY_MCHAT_OPTIONS'				=> 'mChat-Einstellungen Diebstahl',
	'ROBBERY_MCHAT_ENABLE'				=> 'Aktiviere Diebstahl-Beiträge in mChat',
	'ROBBERY_MCHAT_ENABLE_EXPLAIN'		=> 'Sende Beiträge über erfolgreiche und fehlgeschlagene Raubzüge an mChat.',
	'ROBBERY_NOTIFY'					=> 'Sende dem Bestohlenen eine Benachrichtigungs-PN',
	'ROBBERY_NOTIFY_EXPLAIN'			=> 'Dies aktiviert, ob angegriffene Benutzer per PN darüber informiert werden',
	'ROBBERY_OPTIONS'					=> 'Einstellungen Diebstahl',

	'TOP_POINTS'						=> 'Anzuzeigende Anzahl der reichsten Benutzer',
	'TOP_POINTS_EXPLAIN'				=> 'Hier kannst du den Wert für die Anzeige der reichsten Benutzer einstellen. Dies wird von verschiedenen Anzeigen benutzt.',

	'UPLIST_ENABLE'						=> 'Aktiviere Ultimate Points Liste',
	'UPLIST_ENABLE_EXPLAIN'				=> 'Erlaubt Benutzern, die Ultimate Points Liste zu verwenden',
	'USER_POINTS'						=> 'Benutzerpunkte',
	'USER_POINTS_EXPLAIN'				=> 'Anzahl der Punkte, die ein Benutzer besitzt',
));
