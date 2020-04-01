<div class="container">
    <div class="row">
        <div class="col-md-12">
            <p id="sphinxql">&nbsp;</p>
        </div>
        <div class="col-md-12">

            <form method="GET" action="" id="search_form" class="form-inline">
                <div class="input-group" style=" width: 100%;">

                    <input type="text" class="form-control typeahead" name="query" id="query" placeholder="randge"
                           autocomplete="off" value="<?= isset($_GET['query']) ? htmlentities($_GET['query']) : '' ?>"
                           style="    width: 80%;">
                    <input type="submit" class="btn btn-default"
                           id="send" name="send" value="Search">
                </div>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <?php if (isset($didyoumean) && $total_found > 0) : ?>
                <p class="lead">
                    No results found for <b><?php echo $_GET['query']; ?></b>. Showing instead results for
                    <i><?php echo $didyoumeanstring ?></i>
                </p>
            <?php endif ?>
            <?php if (isset($total_found)): ?>
                <p class="lead">
                    Total found:<?= $total_found ?>
                </p>
            <?php endif; ?>
        </div>

        <?php if (count($rows) > 0): ?>
        <div class=" col-md-12">
            <?php if ($total_found > $offset): ?>
                <?php include 'template/paginator.php'; ?>
            <?php endif; ?>
        </div>

        <?php foreach ($rows as $doc): ?>

            <div class="col-md-12">

                <h3>
                    <?= $doc['snippet_movie_title'] ?>
                </h3>
                <p>
                    Director: <?= $doc['snippet_director_name'] ?>
                </p>
                <p>
                    Main Actors: <?= $doc['snippet_actor_1_name'] ?>,<?= $doc['snippet_actor_2_name'] ?>
                    ,<?= $doc['snippet_actor_3_name'] ?>
                </p>
                <p>
                    Plot keywords: <?= $doc['snippet_plot_keywords'] ?>
                </p>
            </div>

        <?php endforeach; ?>
        <div class="col-md-12">
            <?php if ($total_found > $offset): ?>
                <div class="span9"><?php include __DIR__.'/common/template/paginator.php'; ?></div>
            <?php endif; ?>
            <?php elseif (isset($_GET['query']) && $_GET['query'] != ''): ?>
                <p class="lead">Nothing found!</p>
            <?php endif; ?>
        </div>