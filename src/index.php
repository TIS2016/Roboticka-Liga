<?php
require_once(dirname(__FILE__)."/includes/functions.php");
page_head("Letná liga FLL");
page_nav();
get_topright_form();
?>

        <div id="content">

            <script>
                $(document).ready(function(){
                    $("#results").load("includes/show_result_tables.php");
                });
            </script>

            <!-----------------------------------------INDEX PAGE TOP SK----------------------------------------------------->
            <div data-trans-lang="<?php echo SK?>">
                <h2>Vitajte a pozrite si:</h2>
                <a href="prehladZadani.php">Zadania a riešenia letnej ligy</a>
                <h2>Oznamy:</h2>
                <ul>
                    <li><i>Letná liga FLL 2016 beží, pridajte sa! Aj tento rok sa hrá o stavebnicu LEGO MINDSTORMS Education EV3! (slovenská liga)</i></li>
                    <li>V prípade ťažkostí s nahrávaním riešenia ho môžete poslať aj mailom na <i>pavel.petrovic@gmail.com</i></li>
                </ul>

                <h2>Chcete uspieť v tohtoročnom ročníku FLL? Ak áno, riešte letnú ligu!</h2>
                <ul>
                    <li>štartujeme 9. februára</li>
                    <li>bude 10 kôl, ale zapojíť sa môžete do všetkých alebo hoci len do jedného z nich</li>
                    <li>pre viacčlenné tímy vo veku 10-16 rokov (nemusíte byť registrovaní na FLL)</li>
                    <li>každé dva týždne nové zadanie, na riešenie máte 3 týždne</li>
                    <li>vecné ceny</li>
                    <li>fair play a zdravý súťažný duch</li>
                    <li>ani vy nemôžete chýbať!</li>
                </ul>

                <h2>Pravidlá</h2>
                <ul>
                    <li>Na krúžku, v klube alebo doma tím samostatne a načas vyrieši úlohu a odovzdá svoje riešenie na týchto stránkach.</li>
                    <li>Riešenie obsahuje: popis riešenia, spoločné foto vášho tímu, foto robota, program a video ako robot vyrieši úlohu. (<i>Tip: svoje video na YouTube označte ako &quot;unlisted&quot;
                            a nik ho pred termínom odoslania nenájde, aj keď ho tam už budete mať</i>)
                    </li>
                    <li>Môžete použiť iba robotické stavebnice LEGO MINDSTORMS (RCX, NXT, EV3) so základnými senzormi a štandardný programovací jazyk NXT-G, EV3, alebo Robolab.</li>
                    <li>Vaše riešenie získa do celkového ligového hodnotenia 0-3 body.</li>
                    <li>Riešenia hodnotí skupina nezávislých rozhodcov</li>
                    <li>Ak sa vám zdá úloha náročná, zjednodušte si ju podľa potreby!</li>
                </ul>

                <h2>Kompletné výsledky</h2>
            </div>

            <!-----------------------------------------INDEX PAGE TOP ENG----------------------------------------------------->
            <div data-trans-lang="<?php echo ENG?>">
                <h2>Welcome! You shouldn't miss the following:</h2>
                <a href="prehladZadani.php">Assignments and solutions of the summer league</a>
                <h2>Announcements:</h2>
                <ul>
                    <li><i>The summer league 2016 is on, join us! Participants from all over the World are invited to join the Open League - we will not send you prizes, but you will earn the presitige, and most importantly - learn together with all of us! </i></li>
                    <li>In case of any difficulties with uploading a solution, you can send it via e-mail to the following address: <i>pavel.petrovic@gmail.com</i></li>
                </ul>

                <h2>Would you like to be successful in this year's FLL? Join the summer league!</h2>
                <ul>
                    <li>we are starting on February 9th</li>
                    <li>there will be 10 rounds, but you can try to compete in any number of them</li>
                    <li>for teams with multiple members of the ages 10-16 (you don't have to be registered in FLL)</li>
                    <li>a new assignment every 2 weeks with a 3 week deadline</li>
                    <li>fair play and a healthy competitive spirit</li>
                    <li>you can't miss this!</li>
                </ul>

                <h2>Rules</h2>
                <ul>
                    <li>A team solves an assignment in the club or at home and uploads their solution on this website.</li>
                    <li>A solution should contain: description of the solution, a photo of the team, a photo of the robot, the program and a video of the robot solving the problem. (<i>Hint: Nobody will find
                            your video on YouTube if you mark it as &quot;unlisted&quot;, even if you upload it before the current deadline</i>)
                    </li>
                    <li>You can use only LEGO MINDSTORMS (RCX, NXT, EV3) robotic kits with the basic sensors and a standard programing language NXT-G, EV3, or Robolab </li>
                    <li>Your solution will be rated with 0-3 points, which will be added to your current year's progress in the summer league</li>
                    <li>An independent jury is in charge of rating your solutions</li>
                    <li>If you find an assignment too difficult, simplify it according to your needs! (And explain it in the solution)</li>
                </ul>

                <h2>Complete results</h2>
            </div>

            <p id="results"><span  data-trans-key="table-loading"></span></p>

            <?php ob_start(); ?>
            <!-----------------------------------------INDEX PAGE JURY IMAGES----------------------------------------------------->
            <div class="jury-img">
                <img src="hodnot/lubos_miklosovic.jpg"><p>Luboš Miklošovič</p>
                <img src="hodnot/michal_fikar.jpg"><p>Michal Fikar</p>
            </div>
            <div class="jury-img">
                <img src="hodnot/michaela_axamitova.jpg"><p>Michaela Axamitová</p>
                <img src="hodnot/richard_balogh.jpg"><p>Richard Balogh</p>
            </div>
            <?php $juryImages = ob_get_contents();
            ob_end_clean(); ?>

            <!-----------------------------------------INDEX PAGE BOTTOM SK----------------------------------------------------->
            <div data-trans-lang="<?php echo SK?>">
                <h2>Ako hodnotíme?</h2>
                <p>
                    Vaše riešenia si dôkladne prezrú títo štyria ľudia: Mišo a Ľubo - študenti informatiky FMFI UK, Miška z Nexterie, ktorá organizuje FLL a Rišo - líder Robotika.SK:
                </p>
                <?php echo $juryImages?>
                <p>
                    Každý z nich nezávisle od ostatných pridelí 0-3 body podľa toho, či riešenie je kompletné (obsahuje obrázky, video, program, dobrý popis a robot robí to, čo má) a nakoľko ich zaujme. Do tabuľky sa vám započíta aritmetický priemer.
                </p>
                <h2>Predchádzajúce ročníky Letnej ligy:</h2>
                <ul>
                    <li><strong><a href="http://www.fll.sk/archiv/2014/ll" target="_top">Letná liga 2014</a></strong></li>
                    <li><strong><a href="http://www.fll.sk/archiv/2013/letnaliga" target="_top">Letná liga 2013</a></strong></li>
                </ul>

                <br>
                <p><i >Poznámka: Letná liga nie je priamou súčasťou FLL, je určená na predsúťažný tréning a pripravuje ju združenie <a href="http://robotika.sk/" target="_top">Robotika.SK</a></i></p>

            </div>

            <!-----------------------------------------INDEX PAGE BOTTOM ENG----------------------------------------------------->
            <div data-trans-lang="<?php echo ENG?>">
                <h2>How do we rate?</h2>
                <p>
                    Your solutions will be precisely analysed by these 4 people: Mišo and Ľubo - IT students of FMFI IK, Miška from Nexteria, which is organizing FLL, and Rišo - leader of Robotika.SK:
                </p>
                <?php echo $juryImages?>
                <p>
                    Every one of them will independently assign 0-3 points, depending on the originality and completeness of the solution (it contains pictures, a video, a program,
                    a good description and the robot is doing what he should be doing). The arithmetic mean of their ratings will be added to the table.
                </p>
                <h2>Previous years of the summer league:</h2>
                <ul>
                    <li><strong><a href="http://www.fll.sk/archiv/2014/ll" target="_top">Summer league 2014</a></strong></li>
                    <li><strong><a href="http://www.fll.sk/archiv/2013/letnaliga" target="_top">Summer league 2013</a></strong></li>
                </ul>

                <br>
                <p><i>Note: The summer league is not a direct part of FLL, it should be considered as a pre-competition training brought by <a href="http://robotika.sk/"
                                                                                                                                               target="_top">Robotika.SK</a></i></p>
            </div>

        </div>

<?php
page_footer();
?>
