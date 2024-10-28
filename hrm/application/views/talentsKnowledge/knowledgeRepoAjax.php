<div class="col-md-12">

    <?php
    $count = 0;
    foreach ($knowledgeRepo as $knowledge) {
        ?>
        <div class="col-sm-6">
            <div class="col-sm-12">
                <br>
                <!-- Widget: user widget style 1 -->
                <div class="box box-widget widget-user-2">
                    <!-- Add the b g color to the header using any of the bg-* classes -->
                    <div class="box box-widget widget-user-2">
                        <!-- Add the bg color to the header using any of the bg-* classes -->
                        <div class="widget-user-header bg-light-blue-gradient" style="border: medium none; border-radius: 2px 20px">

                            <!-- /.widget-user-image -->
                            <h3 class="widget-user-username"> <?php
                                echo anchor('knowledgeRepo/readMore/' . $knowledge['id'], $knowledge['heading'], array(
                                    'target' => '_blank'
                                ));
                                ?>
                            </h3>
                            <h5 class="widget-user-desc text-right"><?php echo 'Created By : ' . $knowledge['name']; ?><br/></h5>
                            <?php
                            $tags = $this->Tag->getSelectedTagByKnowledgeId($knowledge['id']);
                            $tagName = "";
                            foreach ($tags as $tag) {
                                if ($tagName) {
                                    $tagName .= ',' . $tagName;
                                } else {
                                    $tagName = $tagName;
                                }
                            }
                            ?>
                            <h5 class="widget-user-desc"><?php //echo 'Tags: ' . $tagName;  ?></h5>
                        </div>
                        <div class="box-footer">
                            <p> <?php
                                $reducedDescription = $this->KnowledgeRepository->reduceLength($knowledge['description'], 130);
                                echo $reducedDescription;
                                echo anchor('knowledgeRepo/readMore/' . $knowledge['id'], 'read more', array(
                                    'target' => '_blank'
                                ));
                                ?></p>
                        </div>
                    </div>
                </div>
                <!-- /.widget-user -->
                <!-- prev code here -->




            </div>
        </div>
        <?php
        $count++;
    }
    if ($count == 0) {
        echo '<br/><br/><br/>No Knowledge Found';
    }
    ?>
</div>
<div class="col-md-12">
    <?php echo $this->ajax_pagination->create_links(); ?>
</div>

