<?php

namespace App\Controller;

use App\Entity\Book;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    public function __construct(private ManagerRegistry $doctrine){}
    #[Route('/book', name: 'app_book')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/BookController.php',
        ]);
    }

    #[Route('/api/create', name: 'app_create_api', methods: 'POST')]
    public function create_api(Request $request): JsonResponse
    {
        $book = new Book();
        $parameter = json_decode($request->getContent(),true);

        $folderPath = "upload/";
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);

        $image_parts = explode(";base64,", $request->fileSource);

        $image_type_aux = explode("image/", $image_parts[0]);

        $image_type = $image_type_aux[1];

        $image_base64 = base64_decode($image_parts[1]);

        $file_name=uniqid() . '.png';
        $file = $folderPath . $file_name;

        file_put_contents($file, $image_base64);


        $book->setAuthor($parameter['author']);
        $book->setTitle($parameter['title']);
        $book->setDescription($parameter['description']);
        $book->setImage('C:\xampp\htdocs\book-reviews-app\my-backend\public\upload\\'.$file_name);
        $book->setPublishedAt(\DateTime::createFromFormat('Y-m-d',$parameter['published_at']));
        $book->setReview($parameter['review']);
        $book->setGrade($parameter['grade']);

        $em = $this->doctrine->getManager();
        $em->persist($book);
        $em->flush();

        return $this->json('Created sucessfully');
    }

    #[Route('/api/update/{id}', name: 'app_update_api', methods: 'PUT')]
    public function update_api(Request $request, $id): JsonResponse
    {
        $data = $this->doctrine->getRepository(Book::class)->find($id);

        $parameter = json_decode($request->getContent(),true);

        $data->setTitle($parameter['title']);
        $data->setAuthor($parameter['author']);
        $data->setDescription($parameter['description']);
        $data->setImage($parameter['image']);
        $data->setPublishedAt(\DateTime::createFromFormat('Y-m-d',$parameter['published_at']));
        $data->setReview($parameter['review']);
        $data->setGrade($parameter['grade']);

        $em = $this->doctrine->getManager();
        $em->persist($data);
        $em->flush();

        return $this->json('Updated sucessfully');
    }

    #[Route('/api/delete/{id}', name: 'app_delete_api', methods: 'DELETE')]
    public function delete($id): JsonResponse
    {
        $data = $this->doctrine->getRepository(Book::class)->find($id);

        $em = $this->doctrine->getManager();
        $em->remove($data);
        $em->flush();

        return $this->json('Deleted sucessfully');
    }

    #[Route('/api/fetchall', name: 'app_fetchall_api', methods: 'GET')]
    public function fetchall_api(): JsonResponse
    {
        $data = $this->doctrine->getRepository(Book::class)->findAll();
        foreach ($data as $d)
        {
            $res[] = [
                'id' => $d->getId(),
                'title'=> $d->getTitle(),
                'author'=> $d->getAuthor(),
                'description'=>$d->getDescription(),
                'image'=>$d->getImage(),
                'published_at'=>($d->getPublishedAt())->format('d-m-Y'),
                'review'=>$d->getReview(),
                'grade'=>$d->getGrade()

            ];
        }
        return $this->json(
            $res
        );

    }
    #[Route('/api/filter/date/asc', name: 'app_filter_date_asc_api', methods: 'GET')]
    public function filter_date_asc(): JsonResponse
    {

        $data=$this->doctrine->getRepository(Book::class)->getAllBookSortedByDateASC();

        foreach ($data as $d)
        {
            $res[] = [
                'id' => $d->getId(),
                'title'=> $d->getTitle(),
                'author'=> $d->getAuthor(),
                'description'=>$d->getDescription(),
                'image'=>$d->getImage(),
                'published_at'=>($d->getPublishedAt())->format('d-m-Y'),
                'review'=>$d->getReview(),
                'grade'=>$d->getGrade()

            ];
        }
        return $this->json(
            $res
        );
    }

    #[Route('/api/filter/date/desc', name: 'app_filter_date_desc_api', methods: 'GET')]
    public function filter_date_desc(): JsonResponse
    {

        $data=$this->doctrine->getRepository(Book::class)->getAllBookSortedByDateDESC();

        foreach ($data as $d)
        {
            $res[] = [
                'id' => $d->getId(),
                'title'=> $d->getTitle(),
                'author'=> $d->getAuthor(),
                'description'=>$d->getDescription(),
                'image'=>$d->getImage(),
                'published_at'=>($d->getPublishedAt())->format('d-m-Y'),
                'review'=>$d->getReview(),
                'grade'=>$d->getGrade()

            ];
        }
        return $this->json(
            $res
        );
    }

    #[Route('/api/filter/grade/desc', name: 'app_filter_grade_desc_api', methods: 'GET')]
    public function top_headlines(): JsonResponse
    {

        $data=$this->doctrine->getRepository(Book::class)->getAllBookSortedByGradeDESC();

        foreach ($data as $d)
        {
            $res[] = [
                'id' => $d->getId(),
                'title'=> $d->getTitle(),
                'author'=> $d->getAuthor(),
                'description'=>$d->getDescription(),
                'image'=>$d->getImage(),
                'published_at'=>($d->getPublishedAt())->format('d-m-Y'),
                'review'=>$d->getReview(),
                'grade'=>$d->getGrade()

            ];
        }
        return $this->json(
            $res
        );
    }

    #[Route('/api/book/{id}', name: 'app_book_api')]
    public function view($id): JsonResponse
    {
        $d = $this->doctrine->getRepository(Book::class)->find($id);
        $res = [
            'id' => $d->getId(),
            'title'=> $d->getTitle(),
            'author'=> $d->getAuthor(),
            'description'=>$d->getDescription(),
            'image'=>$d->getImage(),
            'published_at'=>($d->getPublishedAt())->format('d-m-Y'),
            'review'=>$d->getReview(),
            'grade'=>$d->getGrade()
        ];

        return $this->json(
            $res
        );

    }
}
