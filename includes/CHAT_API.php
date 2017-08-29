<?php
/**
 * Created by PhpStorm.
 * User: daniq
 * Date: 16-3-2017
 * Time: 15:22
 */
class chats {
    static function loadChats($mysqli, $user) {
        $sql="SELECT * FROM pco_chats WHERE user1='$user' OR user2='$user' ORDER BY last_activity DESC";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        if($count > 0) {
            while($row = mysqli_fetch_assoc($result)) {
                if(chats::checkIfChatHasNewMessages($mysqli, $row['ID'], $user)) {
                    if (strcmp($row['user1'], $user) == 0) {
                        echo '<div class="hover" onclick="openChat(' . $row['ID'] . ')"><img onclick="openUserPage(' . user::getIDFromUUID($mysqli, $row['user2']) . ')" class="avatar pull-left hover" src="' . userpage::getProfilePicture($mysqli, user::getIDFromUUID($mysqli, $row['user2'])) . '" alt="" style="display: block; margin: 0 auto; margin-right:5px;"/>
                        <span><strong>' . user::getNameByUUID($mysqli, $row['user2']) . '</strong></span><br /><span class="text-muted">' . chats::getLastMessage($mysqli, $row['ID']) . '</span><strong><span class="pull-right text-success">'.language::getString($mysqli, 'CHATS_NEW_MESSAGE').'</span></strong>
                        </div><hr />';
                    } else if (strcmp($row['user2'], $user) == 0) {
                        echo '<div class="hover" onclick="openChat(' . $row['ID'] . ')"><img onclick="openUserPage(' . user::getIDFromUUID($mysqli, $row['user1']) . ')" class="avatar pull-left hover" src="' . userpage::getProfilePicture($mysqli, user::getIDFromUUID($mysqli, $row['user1'])) . '" alt="" style="display: block; margin: 0 auto; margin-right:5px;"/>
                        <span><strong>' . user::getNameByUUID($mysqli, $row['user1']) . '</strong></span><br /><span class="text-muted">' . chats::getLastMessage($mysqli, $row['ID']) . '</span><strong><span class="pull-right text-success">'.language::getString($mysqli, 'CHATS_NEW_MESSAGE').'</span></strong>
                        </div><hr />';
                    }
                } else {
                    if (strcmp($row['user1'], $user) == 0) {
                        echo '<div class="hover" onclick="openChat(' . $row['ID'] . ')"><img onclick="openUserPage(' . user::getIDFromUUID($mysqli, $row['user2']) . ')" class="avatar pull-left hover" src="' . userpage::getProfilePicture($mysqli, user::getIDFromUUID($mysqli, $row['user2'])) . '" alt="" style="display: block; margin: 0 auto; margin-right:5px;"/>
                        <span><strong>' . user::getNameByUUID($mysqli, $row['user2']) . '</strong></span><br /><span class="text-muted">' . chats::getLastMessage($mysqli, $row['ID']) . '</span>
                        </div><hr />';
                    } else if (strcmp($row['user2'], $user) == 0) {
                        echo '<div class="hover" onclick="openChat(' . $row['ID'] . ')"><img onclick="openUserPage(' . user::getIDFromUUID($mysqli, $row['user1']) . ')" class="avatar pull-left hover" src="' . userpage::getProfilePicture($mysqli, user::getIDFromUUID($mysqli, $row['user1'])) . '" alt="" style="display: block; margin: 0 auto; margin-right:5px;"/>
                        <span><strong>' . user::getNameByUUID($mysqli, $row['user1']) . '</strong></span><br /><span class="text-muted">' . chats::getLastMessage($mysqli, $row['ID']) . '</span>
                        </div><hr />';
                    }
                }
            }
        } else {
            echo language::getString($mysqli, 'CHATS_NO_CHATS_SENDER_OR_RECEIVED');
        }
    }
    static function getLastMessage($mysqli, $chatid) {
        if(!chats::isChatOfUser($mysqli, $chatid, $_SESSION['UUID'])) {
            echo language::getString($mysqli, 'ERROR');
            exit;
        }
        $sql="SELECT * FROM pco_chats_messages WHERE chat_id='$chatid' ORDER BY ID DESC";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        $row = mysqli_fetch_assoc($result);
        if($count > 0) {
            $string = '';
            if(strlen($row['message']) > 50) {
                $string = '.....';
            }
            return user::getNameByUUID($mysqli, $row['sender']).": ".substr(common::makeUrls($row['message']), 0, 50).$string;
        } else {
            return language::getString($mysqli, 'CHATS_NO_MESSAGES_SENDED');
        }
    }
    static function loadChat($mysqli, $chatid) {
        if(!chats::isChatOfUser($mysqli, $chatid, $_SESSION['UUID'])) {
            echo language::getString($mysqli, 'ERROR');
            exit;
        } else {
            $sql = "SELECT * FROM pco_chats_messages WHERE chat_id='$chatid' ORDER BY ID ASC LIMIT 50";
            $result = mysqli_query($mysqli, $sql);
            $count = mysqli_num_rows($result);
            if ($count > 0) {
                echo '<input type="hidden" id="id" value="' . $chatid . '"/>';
                while ($row = mysqli_fetch_assoc($result)) {
                    echo '<div>
                            <img class="avatar pull-left hover" src="' . userpage::getProfilePicture($mysqli, user::getIDFromUUID($mysqli, $row['sender'])) . '" alt="" style="display: block; margin: 0 auto; margin-right:5px;"/>
                            <span><strong>' . user::getNameByUUID($mysqli, $row['sender']) . '</strong></span><br />
                            <span class="">' . common::makeUrls($row['message']) . '</span><br /><span class="text-muted">' . $row['sended'] . '</span>
                    </div><hr />';
                }
            } else {
                echo '<input type="hidden" id="id" value="' . $chatid . '"/>';
            }
        }
    }
    static function loadChatReload($mysqli, $chatid) {
        if(!chats::isChatOfUser($mysqli, $chatid, $_SESSION['UUID'])) {
            echo language::getString($mysqli, 'ERROR');
            exit;
        }
        $sql1="UPDATE pco_chats_messages SET readed='1' WHERE chat_id='$chatid' AND readed='0' AND sender NOT IN ('".$_SESSION['UUID']."') ORDER BY ID ASC LIMIT 50";
        $result1 = mysqli_query($mysqli, $sql1);

        $sql="SELECT * FROM pco_chats_messages WHERE chat_id='$chatid' ORDER BY ID ASC LIMIT 50";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        if($count > 0) {
            echo '<input type="hidden" id="id" value="'.$chatid.'"/>';

            while($row = mysqli_fetch_assoc($result)) {
                echo '<div>
                        <img class="avatar pull-left hover" src="' . userpage::getProfilePicture($mysqli, user::getIDFromUUID($mysqli, $row['sender'])) . '" alt="" style="display: block; margin: 0 auto; margin-right:5px;"/>
                        <span><strong>' . user::getNameByUUID($mysqli, $row['sender']) . '</strong></span><br />
                        <span class="">'.common::makeUrls($row['message']).'</span><br /><span class="text-muted">'.$row['sended'].'</span>
                </div><hr />';
            }
        } else {
            echo '<input type="hidden" id="id" value="'.$chatid.'"/>';
        }
    }
    static function sendMessage($mysqli, $chatid, $message) {
        if(!chats::isChatOfUser($mysqli, $chatid, $_SESSION['UUID'])) {
            echo language::getString($mysqli, 'ERROR');
            exit;
        }
        if(strcmp($message, '') == 0) {
            return;
        }
        $sql="INSERT INTO pco_chats_messages (chat_id, sender, message, sended) VALUES ('$chatid', '".$_SESSION["UUID"]."', '$message', '".strftime('%e-%m-%Y om %H:%M', time())."')";
        $result = mysqli_query($mysqli, $sql);
        $sql="UPDATE pco_chats SET last_activity=CURRENT_TIMESTAMP() WHERE ID='$chatid'";
        $result = mysqli_query($mysqli, $sql);
    }
    static function getNameOfChatter($mysqli, $chatid) {
        if(!chats::isChatOfUser($mysqli, $chatid, $_SESSION['UUID']) || staff::canManageChats($mysqli, $_SESSION['UUID'])) {
            return language::getString($mysqli, 'ERROR');
        }
        $sql="SELECT * FROM pco_chats WHERE ID='$chatid'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        if($count > 0) {
            $row = mysqli_fetch_assoc($result);
            if(strcmp($row['user1'], $_SESSION['UUID']) == 0) {
                return user::getNameByUUID($mysqli, $row['user2']);
            }
            if(strcmp($row['user2'], $_SESSION['UUID']) == 0) {
                return user::getNameByUUID($mysqli, $row['user1']);
            }
            return language::getString($mysqli, 'ERROR');
        }
    }
    static function isChatOfUser($mysqli, $chatid, $userid) {
        $sql="SELECT * FROM pco_chats WHERE ID='$chatid'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        $row = mysqli_fetch_assoc($result);
        if($count > 0) {
            if(strcmp($userid, $row['user1']) == 0) {
                return true;
            } else if(strcmp($userid, $row['user2']) == 0) {
                return true;
            } else {
                return false;
            }
        }
        return false;
    }
    static function startChat($mysqli, $user1, $user2) {
        if(!user::existUUID($mysqli, $user1) && !user::existUUID($mysqli, $user2)) {
            return;
        }
        $sql="SELECT * FROM pco_chats WHERE user1='$user1' AND user2='$user2' OR user1='$user2' AND user2='$user1'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        $row = mysqli_fetch_assoc($result);
        if($count == 0) {
            $sql="INSERT INTO pco_chats (user1, user2, last_activity) VALUES ('$user1', '$user2', CURRENT_TIMESTAMP())";
            $result = mysqli_query($mysqli, $sql);
        }
    }
    static function getChatID($mysqli, $user1, $user2) {
        $sql="SELECT * FROM pco_chats WHERE user1='$user1' AND user2='$user2' OR user1='$user2' AND user2='$user1'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        $row = mysqli_fetch_assoc($result);
        if($count > 0) {
            return $row['ID'];
        }
    }
    static function countNotReadedMessages($mysqli, $userid) {
        $counter = 0;
        $sql="SELECT * FROM pco_chats_messages WHERE readed='0' AND sender NOT IN ('$userid')";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        while($row = mysqli_fetch_assoc($result)) {
            $chatid = $row['chat_id'];
            $sql1="SELECT * FROM pco_chats WHERE ID='$chatid'";
            $result1 = mysqli_query($mysqli, $sql1);
            $count1 = mysqli_num_rows($result1);
            $row1 = mysqli_fetch_assoc($result1);
            if($count1 > 0) {
                if(strcmp($userid, $row1['user1']) == 0) {
                    $counter++;
                } else if(strcmp($userid, $row1['user2']) == 0) {
                    $counter++;
                }
            }
        }
        return $counter;
    }
    static function checkIfChatHasNewMessages($mysqli, $chatid, $userid) {
        $newmessages = false;
        $sql="SELECT * FROM pco_chats_messages WHERE readed='0' AND sender NOT IN ('$userid') AND chat_id='$chatid'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        while($row = mysqli_fetch_assoc($result)) {
            $newmessages = true;
        }
        return $newmessages;
    }
    static function newChat($mysqli, $keywords) {
        $sql="SELECT * FROM pco_users WHERE name LIKE '%{$keywords}%' LIMIT 20";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        while($row = mysqli_fetch_assoc($result)) {
            echo '
            <div class="col-md-12">
                <img class="avatar pull-left" src="'.userpage::getProfilePicture($mysqli, $row["ID"]).'" alt="" style="display: block; margin: 0 auto;"/><br />
                <span class="pull-left">'.$row['name'].'</span></a><a href="messenger.php?startchat='.$row['UUID'].'" style="color: black; font-weight: bold; display: block; margin: 0 auto; text-align: center;" class="pull-right"><i class="material-icons">add_circle</i></a>
            </div><br/><br/><hr/>
            ';
        }
        if($count == 0) {
            echo '<p><strong>'.language::getString($mysqli, 'CHATS_NO_MATCHING_USERS').'</strong></p>';
        }
    }
}