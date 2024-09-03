TRUNCATE articles, articles_comparison, articles_details, articles_name_variations, 
characteristics_comparison, producers, producers_comparison, producers_dsts_names,
producers_name_variations;

DROP TABLE articles, articles_comparison, articles_details, articles_name_variations, 
characteristics_comparison, producers, producers_comparison, producers_dsts_names,
producers_name_variations;

SELECT * FROM articles
INNER JOIN articles_details ON articles_details.article_id = articles.id
WHERE article_name = 'P550777';