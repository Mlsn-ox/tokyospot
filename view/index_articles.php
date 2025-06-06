<?php
require_once "../config.php";
$menu = "index";
?>
<!DOCTYPE html>
<html lang="fr" data-bs-theme="light">

<head>
    <?php require_once "../includes/head.php"; ?>
    <title>Spots - TokyoSpot</title>
    <meta name="description" content="Spots- TokyoSpot">
</head>

<body>
    <?php require_once "../includes/navbar.php";
    $keyword = $_GET['search'] ?? '';
    $keyword = trim($keyword);
    $category = $_GET['cat'] ?? '';
    $params = []; // tableau pour les paramètres
    $sql = "SELECT article.art_id, article.art_title, article.art_content, image.img_name, category.cat_name 
            FROM article 
            LEFT JOIN image ON image.img_fk_art_id = article.art_id
            LEFT JOIN category ON article.art_fk_cat_id = category.cat_id 
            WHERE article.art_status = 'approved' ";
    if ($category !== '') {
        $sql .= " AND article.art_fk_cat_id = :cat";
        $params['cat'] = $category;
    }
    if ($keyword !== '') {
        $sql .= " AND (art_title LIKE :search OR art_content LIKE :search)";
        $params['search'] = "%$keyword%";
    }
    $sql .= " ORDER BY art_created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>
    <div class="section container-xxl p-xl-3 p-lg-2 p-1 mx-auto home d-flex flex-column align-items-center">
        <div class="container-fluid drop-container">
            <div class="d-flex justify-content-center filters mt-4 mt-lg-2 mb-4">
                <button class="d-flex align-items-center justify-content-center px-3 gap-2 dropfilters rounded"
                    type="button" data-bs-toggle="collapse" data-bs-target="#collapseFilters" aria-expanded="false" aria-controls="collapseFilters">
                    <?= ($keyword == '' && $category == '') ? 'Rechercher ⤵' : 'Fermer ⤴' ?>
                </button>
            </div>
            <div class="collapse <?= ($keyword == '' && $category == '') ? '' : 'show' ?> rounded mx-auto mb-4" id="collapseFilters">
                <div class="container-fluid mb-1">
                    <form method="GET" class="d-flex flex-wrap justify-content-evenly align-items-center" id="filter">
                        <div class="col-md-5 col-sm-8 col-12">
                            <label for="search" class="form-label">Rechercher par mots clés 🕵️‍♂️</label>
                            <input type="text" class="form-control" name="search" aria-label="Rechercher un article" maxlength="50">
                        </div>
                        <div class="col-md-5 col-sm-8 col-12">
                            <label for="category" class="form-label">Catégorie</label>
                            <select id="category" class="form-select category" name="cat" aria-label="Choix de la catégorie">
                                <option value="">Toutes</option>
                                <?php
                                try {
                                    $sql = "SELECT * FROM category;";
                                    $stmt = $pdo->query($sql);
                                    $cats = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    foreach ($cats as $cat) {
                                        echo "<option value='" . $cat['cat_id'] . "'" . $selected . ">" . getEmojiCategory($cat['cat_name']) . " " . ucfirst($cat['cat_name']) . "</option>";
                                    }
                                } catch (PDOException $e) {
                                    echo "<option disabled>Erreur lors du chargement des catégories</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="btn-filters d-flex flex-wrap justify-content-center align-items-center mt-3 gap-3">
                            <button type="submit" class="btn btn-outline-primary rounded-pill ">Afficher</button>
                            <button type="reset" class="btn btn-sm btn-outline-danger rounded-pill " id="reset">Réinitialiser</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="container-fluid row justify-content-around flex-wrap" id="articles">
            <?php
            if ($articles) {
                foreach ($articles as $article) { ?>
                    <a href="read_article.php?id=<?= $article['art_id'] ?>" class="article mb-3"
                        style="background-image: url('<?= $config['url'] ?>/assets/img_articles/<?= $article['img_name'] ?>');">
                        <div class="article-content">
                            <h2 class="mx-1"><?= $article['art_title'] ?></h2>
                            <div class="content">
                                <p class="m-0 categorie">
                                    <?= getEmojiCategory($article['cat_name']) ?> <?= $article['cat_name'] ?>
                                </p>
                                <p><?= $article['art_content'] ?></p>
                            </div>
                        </div>
                    </a>
                <?php }
            } else { ?>
                <div class='col-12 text-center my-5'>
                    <h2>Aucun article trouvé</h2>
                </div>
            <?php } ?>
        </div>
    </div>
    <script type="module" src="<?= $config['url'] ?>/script/index.js"></script>
    <?php require_once "../includes/footer.php" ?>
</body>

</html>