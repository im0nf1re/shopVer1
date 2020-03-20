<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    function translit($s)
    {
        $s = (string)$s; // преобразуем в строковое значение
        $s = strip_tags($s); // убираем HTML-теги
        $s = str_replace(array("\n", "\r"), " ", $s); // убираем перевод каретки
        $s = preg_replace("/\s+/", ' ', $s); // удаляем повторяющие пробелы
        $s = trim($s); // убираем пробелы в начале и конце строки
        $s = function_exists('mb_strtolower') ? mb_strtolower($s) : strtolower($s); // переводим строку в нижний регистр (иногда надо задать локаль)
        $s = strtr($s, array('а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'e', 'ж' => 'j', 'з' => 'z', 'и' => 'i', 'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c', 'ч' => 'ch', 'ш' => 'sh', 'щ' => 'shch', 'ы' => 'y', 'э' => 'e', 'ю' => 'yu', 'я' => 'ya', 'ъ' => '', 'ь' => ''));
        $s = preg_replace("/[^0-9a-z-_ ]/i", "", $s); // очищаем строку от недопустимых символов
        $s = str_replace(" ", "-", $s); // заменяем пробелы знаком минус
        return $s; // возвращаем результат
    }

    /**
     * @Route("/product/{prod}", name="product")
     */
    public function show_product($prod) {
        $product = $this->getDoctrine()->getRepository(Product::class)->findOneBy(array('eng_name' => $prod));
        if (!$product) {
            return $this->createNotFoundException('product not found');
        }

        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();

        return $this->render('product/index.html.twig', [
            'product' => $product,
            'categories' => $categories,
        ]);
    }

    /**
     * @Route("/add_product", name="add_product")
     */
    public function add_product()
    {
        $message = 'Добавьте продукт';
        if (isset($_POST['btn']) &&
            (
                !empty($_POST['name']) ||
                !empty($_POST['price']) ||
                !empty($_POST['description']) ||
                !empty($_POST['category'])
            )
        ) {
            $category = $this->getDoctrine()->getRepository(Category::class)->find($_POST['category']);
            $entityManager = $this->getDoctrine()->getManager();
            $product = new Product();
            $product->setName($_POST['name']);
            $product->setEngName($this->translit($_POST['name']));
            $product->setPrice($_POST['price']);
            $product->setDescription($_POST['description']);
            $product->setCategory($category);
            $product->setImagePath($_POST['image_path']);

            $entityManager->persist($product);
            $entityManager->flush();

            $message = "продукт " . $product->getName() . ' добавлен';
        }

        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();

        return $this->render('product/add.html.twig', [
            'categories' => $categories,
            'message' => $message
        ]);
    }

    /**
     * @Route("/remove_product", name="remove_product")
     */
    public function remove_product() {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        $message = "Удалите продукт";
        if (isset($_POST['btn'])) {
            $entityManager = $this->getDoctrine()->getManager();
            $product = $entityManager->getRepository(Product::class)->findBy(array('name' => $_POST['name']));

            if(!$product) {
                $message = 'Продукта '.$_POST['name'].' не существует';
            } else {
                foreach ($product as $item) {
                    $entityManager->remove($item);
                }
                $entityManager->flush();
                $message = 'Продукт '.$_POST['name'].' удален';
            }
        }
        return $this->render('product/remove.html.twig',[
            'message' => $message,
            'categories' => $categories
        ]);
    }

}
