    <?php
/**
 * Created by PhpStorm.
 * User: daniq
 * Date: 2-8-2017
 * Time: 17:19
 */
class language
{
    static function getString($mysqli, $string)
    {
        $lang = array(
            'NL' => array(
                'ERROR' => 'Error',
                'COMPANY_NAME' => 'ParkCraft',
                'NO_ACCESS' => 'Je hebt geen toegang tot ParkCraft Online.',

                'CHATS_NEW_MESSAGE' => 'Nieuw bericht!',
                'CHATS_NO_CHATS_SENDER_OR_RECEIVED' => 'Nog geen chats verstuurd of ontvangen!',
                'CHATS_NO_MESSAGES_SENDED' => 'Nog geen berichten verstuurd.',
                'CHATS_NO_MATCHING_USERS' => 'Geen overeenkomende gebruikers!',

                'FEED_POSTED' => 'Geplaatst op: ',

                'NAV_BRAND' => 'ParkCraft Online',
                'NAV_HOME' => 'Home',
                'NAV_PARKS' => 'Parken',
                'NAV_PARK_REQUEST' => 'Park aanvragen',
                'NAV_TUTORIALS' => 'Tutorials',
                'NAV_POTW' => 'Plugin van de week',
                'NAV_POTM' => 'Park van de maand',
                'NAV_VIDEOS' => 'Video\'s',
                'NAV_EVENTS' => 'Evenementen',
                'NAV_PARKLIST' => 'Parken lijst',
                'NAV_AUTHORS' => 'Auteurs',
                'NAV_WRITE_TUTORIAL' => 'Tutorial schrijven',
                'NAV_WRITE_POTW' => 'Plugin van de week schrijven',
                'NAV_WRITE_POTM' => 'Park van de maand schrijven',
                'NAV_VACANCIES' => 'Vacatures ',
                'NAV_PLUGINS' => 'Plugins ',
                'NAV_SEARCH' => 'Zoeken',
                'NAV_MESSAGES' => 'Berichten ',
                'NAV_STAFFPANEL' => 'Staf paneel',
                'NAV_SETTINGS' => 'Instellingen',
                'NAV_FOLLOWED' => 'Volgend',
                'NAV_HELP' => 'Help',
                'NAV_PROFILE' => 'Profiel',
                'NAV_LOGOUT' => 'Uitloggen',
                'NAV_LOGIN' => 'Inloggen',

                'FOLLOWED_LOGO' => 'Logo',
                'FOLLOWED_NAME' => 'Naam',
                'FOLLOWED_OPTIONS' => 'Opties',

                'FOLLOWED_UNFOLLOW' => 'Ontvolgen',
                'FOLLOWED_NOFOLLOWING' => 'Je volgt geen parken.',


                'USERS_NAME' => 'Naam',
                'USER_EMAIL' => 'Email',
                'USER_RANK' => 'Rank',
                'USER_ACCESS' => 'Toegang',
                'USER_ACTIVATED' => 'Geactiveerd',
                'USER_LASTONLINE' => 'Laatst Online',
                'USER_OPTIONS' => 'Opties',
                'USER_PREFIX' => 'Prefix',

                'YES' => 'Ja',
                'NO' => 'Nee',
                'SEE' => 'Bekijken',
                'PREVIOUS' => 'Terug',
                'NEXT' => 'Volgende',

                'NO_USERS_FOUND_ON_THIS_PAGE' => 'Geen gebruikers gevonden op deze pagina.',
                'SPECIFIC_SEARCH' => 'Geef een specefiekere zoekopdracht!',

                'ROLE_SENIORDEVELOPER' => 'Senior Developer',
                'ROLE_BETATESTER' => 'Beta-Tester',
                'ROLE_AUTHOR' => 'Auteur',
                'ROLE_JUNIORDEVELOPER' => 'Junior Developer',
                'ROLE_OWNER' => 'Beheerder',
                'ROLE_VIDEOCREATOR' => 'Video creator',
                'ROLE_MODERATOR' => 'Moderator',
                'ROLE_USER' => 'Gebruiker',

                'SAVE' => 'Opslaan',
                'REMOVE' => 'Verwijder',
                'IP' => 'IP',
                'REMOTE' => 'Overnemen',
                'NO_ACCESS_TO_THIS_SECTION' => 'Geen toegang tot dit gedeelte.',
                'NO_COMMENTS_FOUND_ON_THIS_PAGE' => 'Geen reacties gevonden op deze pagina.',
                'NO_ARTICLES_FOUND_ON_THIS_PAGE' => 'Geen artikelen gevonden op deze pagina.',

                'PARK_NO_POSTS' => 'Dit park heeft nog geen artikelen gepost.',
                'POST_WAIT_FOR_CONFIRMATION' => 'Wacht op bevestiging',
                'PARK_OWNER' => 'Eigenaar',
                'RECTION_ON_REACTION' => 'Er is een reactie geplaatst op een artikel waar jij op hebt gereageerd:',
                'REACTION_ON' => 'Reactie op',
                'ARTICLE_DELETED' => 'Artikel verwijderd',
                'ARTICLE' => 'Artikel',
                'REACTION' => 'Reactie',
                'DELETE_UNDO' => 'Verwijderen ongedaan maken',

                'ACTIVE_POSTS' => 'Actieve Posts',
                'REVIEW' => 'Review',
                'REJECTED' => 'Afgewezen',
                'PARK' => 'Park',
                'COMMENTS' => 'Reacties',

                'NO_LIKES' => 'Geen likes.',

                'CHANGE_MEMBER' => 'Staflid wijzigen',
                'CAN_WRITE_ARTICLES' => ''

            ),
            'EN' => array(
            )
        );

        return $lang[self::getLanguageOfUser($mysqli)][$string];
    }
    static function getLanguageOfUser($mysqli) {
        $sql="SELECT * FROM pco_users WHERE UUID='".$_SESSION['UUID']."'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        $row = mysqli_fetch_assoc($result);
        return empty($row['lang']) ? 'NL' : $row['lang'];
    }
}
?>