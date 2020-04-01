<div class="container-fluid">
    <form method="GET" action="" id="faceted" class="">

        <div class="row">
            <div class="col-sm-12">
                <div class="form-inline">
                    <input type="text" class="form-control mr-sm-2" name="query" id="query"
                           autocomplete="off"
                           value="<?= isset($_GET['query']) ? htmlentities($_GET['query']) : '' ?>"
                           style="    width: 80%;">
                    <input type="submit" class="btn btn-primary mr-sm-2"
                           id="send" name="send" value="Search">
                    <input type="reset" class="btn btn-default mr-sm-2" name="reset" value="Reset">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-3">
                <?php foreach ($facets as $attr => $facet): ?>
                    <div class="row  m-1">
                        <div class="card w-100">

                            <div class="card-header"><?php echo $faceted_name[$attr]; ?></div>
                            <div class="card-body">

                                <?php foreach ($facet as $item): ?>
                                    <?php if ($item[$faceted_map[$attr]] != '' || $item[$faceted_map[$attr]] != 0): ?>

                                        <div class="form-check">
                                            <?php if ($attr == 'content_rating'): ?>

                                                <input type="checkbox" class="form-check-input"
                                                       name="<?php echo $attr; ?>[]" <?php echo (isset($_GET[$attr]) && in_array($item[$faceted_map[$attr]], $_GET[$attr])) ? 'checked' : ''; ?>
                                                       value="<?php echo $item[$faceted_map[$attr]]; ?>">
                                                <label class="form-check-label">
                                                    <?php echo $item[$faceted_map[$attr]]; ?>
                                                    (<?php echo $item['count(*)']; ?>) </label>
                                            <?php else: ?>
                                                <input type="radio" class="form-check-input"
                                                       name="<?php echo $attr; ?>" <?php echo (isset($_GET[$attr]) && $_GET[$attr] == $item[$faceted_map[$attr]]) ? 'checked' : ''; ?>
                                                       value="<?php echo $item[$faceted_map[$attr]]; ?>">
                                                <label class="form-check-label">
                                                    <?php if ($attr == 'title_year') {
                                                        echo (1900 + 10 * $item[$faceted_map[$attr]]) . ' - ' . (1900 + 10 * $item[$faceted_map[$attr]] + 9);
                                                    } else if ($attr == 'imdb_score') {
                                                        echo number_format($item[$faceted_map[$attr]], 1) . ' - ' . ($item[$faceted_map[$attr]] + 0.9);
                                                    } else {
                                                        echo $item[$faceted_map[$attr]];
                                                    } ?> (<?php echo $item['count(*)']; ?>) </label>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                                <?php if (isset($_GET[$attr])): ?>
                                    <a href="#" class="reset_facet" data-target="<?php echo $attr; ?>">Remove</a>
                                <?php endif; ?>

                            </div>
                        </div>
                    </div>

                <?php endforeach; ?>
            </div>
            <div class="col-sm-9">
                <div class="row">
                    <div class="col-sm-12">
                        <p id="sphinxql">&nbsp;</p>
                    </div>

                    <div class="col-sm-12">
                        <?php if (isset($total_found)): ?>
                            <p class="lead">
                                Total found:<?= $total_found ?>
                            </p>
                        <?php else: ?>

                            <p class="lead">Nothing found!</p>
                        <?php endif; ?>
                    </div>

                </div>
                <div class="row">
                    <?php if ($total_found > $offset): ?>
                        <div class="col-sm-12">
                            <?php include 'template/paginator.php'; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <?php foreach ($rows as $doc): ?>
                    <div class="row">


                        <div class="col-sm-12">

                            <h3>
                                <?= $doc['movie_title'] ?>
                            </h3>
                            <p>
                                Release year: <?= $doc['title_year'] ?>
                            </p>
                            <p>
                                Director: <?= $doc['director_name'] ?>
                            </p>
                            <p>
                                Main Actors: <?= $doc['actor_1_name'] ?>,<?= $doc['actor_2_name'] ?>
                                ,<?= $doc['actor_3_name'] ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
                <div class="row">
                    <?php if ($total_found > $offset): ?>
                        <div class="col-sm-12">
                            <?php include getcwd().'/common/template/paginator.php'; ?>
                        </div>

                    <?php endif; ?>
                </div>
            </div>

    </form>
</div>