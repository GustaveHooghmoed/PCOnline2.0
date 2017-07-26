<?php
/**
 * Created by PhpStorm.
 * User: daniq
 * Date: 19-3-2017
 * Time: 18:45
 */
class feed {
    static function getFollowedParks($mysqli, $uuid) {
        $sql="SELECT * FROM pco_parks WHERE followers LIKE '%{$uuid}%'";
        $result=mysqli_query($mysqli,$sql);
        $count=mysqli_num_rows($result);
        $parks = "";
        if($count > 0){
            $counter = 1;
            while($row = mysqli_fetch_assoc($result)) {
                if($count <= $counter) {
                    $parks = $parks." ".$row['ID'];
                } else {
                    $parks = $parks." ".$row['ID'].",";
                }
                $counter++;
            }
        }
        return $parks;
    }
    static function loadArticles($mysqli, $pageid, $uuid) {
        $pagefeed = $pageid*10;
        $parksfollowed = feed::getFollowedParks($mysqli, $uuid);
        $sql = "SELECT * FROM pco_posts WHERE deleted='0' AND reviewed='1' AND park_id IN (".$parksfollowed.") order by ID desc LIMIT $pagefeed, 10";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        if(user::isPlayerFollowingAnyPark($mysqli, $_SESSION['UUID'])) {
            while ($row = mysqli_fetch_assoc($result)) {
                if (user::IsFollowingPark($mysqli, $row['park_id'], $_SESSION['UUID']) || $row['park_id'] == 18 && !park::isDeleted($mysqli, $row['park_id'])) {
                    $title = $row['post_title'];
                    $postid = $row['ID'];
                    $parkname = park::getName($mysqli, $row['park_id']);
                    $logo = park::getLogo($mysqli, $row['park_id']);
                    $post = common::random(20);
                    $postheader = $row['post_header'];
                    if(strpos($postheader, 'Invalid URL') !== false) {
                        $postheader = park::getHeader($mysqli, $row['park_id']);
                    }
                    $icon = '';
                    if(article::isLiking($mysqli, $postid, $_SESSION['UUID'])) {
                        $icon = 'favorite';
                    } else {
                        $icon = 'favorite_border';
                    }
                    $like = '';
                    if(article::isLiking($mysqli, $postid, $_SESSION['UUID'])) {
                        $like = 'unlike';
                    } else {
                        $like = 'like';
                    }
                    echo '
                        <div class="hover" id="' . $post . '">
                            <div>
                                <img class="avatar" src="' . $logo . '" alt=""/>
                                <a href="park.php?id=' . $row['park_id'] . '" style="color: black; font-weight: bold;"><span>' . $parkname . '</span></a>
                            </div>
                            <div>
                                <div>
                                    <img src="' . $postheader . '" alt="header" class="img-responsive center-block" style="max-height: 300px;"/>
                                </div>
                                <h3>' . $title . '</h3>
                            </div>
                            <script>
                                var id' . $post . ' = document.getElementById("' . $post . '");

                                id' . $post . '.onclick = function() {
                                    window.location.href = "article.php?id=' . $postid . '";
                                };
                            </script>
                            <span class="shortcut"><i class="material-icons heart"><a href="article.php?id='.$postid.'&'.$like.'" style="text-decoration: none;">'.$icon.'</a></i><span><a href="article.php?id='.$postid.'&likes" style="color: #000000; text-decoration: none;">'.article::countLikes($mysqli, $postid).'</a>
                            <span class="shortcut"><i class="material-icons">mode_comment</i><span>'.article::getReactionCount($mysqli, $postid).'</span></span>
                            <span class="shortcut"><i class="material-icons">remove_red_eye</i><span>'.statistics::getCountsArticles($mysqli, $postid).'</span></span>
                            <i style="float: right;">Geplaatst op: '.$row["posted_on"].'</i>
                        </div>
                        <hr />

                        ';
                }
            }
        } else {
            $sql = "SELECT * FROM pco_posts WHERE deleted='0' AND reviewed='1' order by ID desc LIMIT $pagefeed, 10";
            $result = mysqli_query($mysqli, $sql);
            $count = mysqli_num_rows($result);
            while ($row = mysqli_fetch_assoc($result)) {
                if (!park::isDeleted($mysqli, $row['park_id'])) {
                    $title = $row['post_title'];
                    $postid = $row['ID'];
                    $parkname = park::getName($mysqli, $row['park_id']);
                    $logo = park::getLogo($mysqli, $row['park_id']);
                    $post = common::random(20);
                    $postheader = $row['post_header'];
                    if(strpos($postheader, 'Invalid URL') !== false) {
                        $postheader = park::getHeader($mysqli, $row['park_id']);
                    }
                    $icon = '';
                    if(article::isLiking($mysqli, $postid, $_SESSION['UUID'])) {
                        $icon = 'favorite';
                    } else {
                        $icon = 'favorite_border';
                    }
                    $like = '';
                    if(article::isLiking($mysqli, $postid, $_SESSION['UUID'])) {
                        $like = 'unlike';
                    } else {
                        $like = 'like';
                    }
                    echo '
                        <div class="hover">
                            <div>
                                <img class="avatar" src="' . $logo . '" alt=""/>
                                <a href="park.php?id=' . $row['park_id'] . '" style="color: black; font-weight: bold;"><span>' . $parkname . '</span></a>
                            </div>
                            <div class="hover" id="' . $post . '">
                                <div>
                                    <img src="' . $postheader . '" alt="header" class="img-responsive center-block" style="max-height: 300px;"/>
                                </div>
                                <h3>' . $title . '</h3>
                            </div>
                            <script>
                                var id' . $post . ' = document.getElementById("' . $post . '");

                                id' . $post . '.onclick = function() {
                                    window.location.href = "article.php?id=' . $postid . '";
                                };
                            </script>
                            <span class="shortcut"><i class="material-icons heart"><a href="article.php?id='.$postid.'&'.$like.'" style="text-decoration: none;">'.$icon.'</a></i><span><a href="article.php?id='.$postid.'&likes" style="color: #000000; text-decoration: none;">'.article::countLikes($mysqli, $postid).'</a>
                            <span class="shortcut"><i class="material-icons">mode_comment</i><span>'.article::getReactionCount($mysqli, $postid).'</span></span>
                            <span class="shortcut"><i class="material-icons">remove_red_eye</i><span>'.statistics::getCountsArticles($mysqli, $postid).'</span></span>
                            <i style="float: right;">Geplaatst op: '.$row["posted_on"].'</i>
                        </div>
                        <hr />

                        ';
                }
            }
        }
    }
}
