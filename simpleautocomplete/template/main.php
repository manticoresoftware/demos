<div class="container">
    <div class="row">
        <div class="col-md-12">
            <p id="sphinxql">&nbsp;</p>
        </div>
        <div class="col-md-12">

            <form method="GET" action="" id="search_form" class="form-inline">
                <div class="input-group" style="    width: 100%;">

                    <input type="text" class="form-control typeahead" name="query" id="query" placeholder="Start typing here"
                           autocomplete="off" value="<?= isset($_GET['query']) ? htmlentities($_GET['query']) : '' ?>"
                           style="    width: 80%;">
                    <input type="submit" class="btn btn-default"
                           id="send" name="send" value="Search">
                </div>
            </form>
        </div>

        <div class="col-md-12">

            <p class="lead">
                <?php if (isset($total_found,$_GET['query'])): ?>
                    Total found:<?= $total_found ?>
                <?php endif; ?>
            </p>
        </div>

    </div>
    <div class="row">
        <div class="span" style="display: none;"></div>
        <?php if (count($rows) > 0): ?>
            <?php if ($total_found > $offset): ?><div class="col-md-12"><?php include 'template/paginator.php'; ?></div><?php endif;?>
            <?php foreach ($rows  as $doc): ?>
                <div class="col-md-12">

                    <h3>
                        <?= $doc['snippet_field'] ?>
                    </h3>
                    <p>
                        Release year: <?= $doc['title_year'] ?>
                    </p>
                    <p>
                        Director: <?= $doc['director_name'] ?>
                    </p>



                </div>
            <?php endforeach; ?>
            <?php if ($total_found > $offset): ?><div class="col-md-12"><?php include __DIR__.'/common/template/paginator.php'; ?></div><?php endif;?>
        <?php elseif (isset($_GET['query']) && $_GET['query'] != ''): ?>
            <p class="lead">Nothing found!</p>
        <?php endif; ?>
    </div>