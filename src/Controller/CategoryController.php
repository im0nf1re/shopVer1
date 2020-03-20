<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
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
     * @Route("/category/{category}", name="category")
     */
    public function show_category($category)
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        $category = $this->getDoctrine()->
            getRepository(Category::class)->findOneBy(array('eng_name' => $category));
        $products = $category->getProducts();
        return $this->render('category/index.html.twig', [
            'category' => $category,
            'categories' => $categories,
            'products' => $products
        ]);
    }

    /**
     * @Route("/add_category", name="add_category")
     */
    public function add_category() {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        $message = 'Добавьте категорию';
        if (isset($_POST['btn']) && !empty($_POST['name'])) {
            $entityManager = $this->getDoctrine()->getManager();
            $category = new Category();
            $category->setName($_POST['name']);
            $category->setEngName($this->translit($_POST['name']));
            $entityManager->persist($category);
            $entityManager->flush();

            $message = 'Категория '.$category->getName().' добавлена';
        }
        return $this->render('category/add.html.twig', [
            'message' => $message,
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/remove_category", name="remove_category")
     */
    public function remove_category() {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        $message = "Удалите категорию";
        if (isset($_POST['btn'])) {
            $entityManager = $this->getDoctrine()->getManager();
            $category = $entityManager->getRepository(Category::class)->findBy(array('name' => $_POST['name']));

            if(!$category) {
                $message = 'Категории '.$_POST['name'].' не существует';
            } else {
                foreach ($category as $item) {
                    $entityManager->remove($item);
                }
                $entityManager->flush();
                $message = 'Категория '.$_POST['name'].' удалена';
            }
        }
        return $this->render('category/remove.html.twig',[
            'message' => $message,
            'categories' => $categories
        ]);
    }
}
