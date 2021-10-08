<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Variant;
use App\Models\Quiz;
use App\Models\Question;
use Illuminate\Http\Request;
use App\Http\Resources\Question as QuestionResource;
use App\Http\Resources\QuestionCollection;
use App\Http\Resources\Variant as VariantResource;
use App\Http\Resources\VariantCollection;
use App\Http\Resources\Quiz as QuizResource;
use App\Http\Resources\QuizCollection;
use Symfony\Component\CssSelector\Node\FunctionNode;
use Validator;

use function PHPUnit\Framework\isNull;

class QuizController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     
    public function __construct()
    {
        $this->middleware('bearer-auth')->only(['store', 'getPersonalQuizzes', 'update', 'destroy', 'updateQuiz']);
    }

    

    public function index()
    {
        $quizzes = Quiz::all();

        if(!$quizzes) {
            return response()->json([
                'status' => false,
                'message' => 'Quizzes not found'
            ])->setStatusCode(404, 'Quizzes not found');
        }

        return response()->json([
            'status' => true,
            'message' => 'Quizzes found',
            'quizzes' => new QuizCollection($quizzes)
        ]);
    }


    public function getPersonalQuizzes(Request $request)
    {
        $token = $request->bearerToken();

        $user = User::where('token', $token)->first();

        $quizzes = Quiz::where('user_id', $user['id'])->orderBy('updated_at', 'desc')->get();

        if(!$quizzes) {
            return response()->json([
                'status' => false,
                'message' => 'Quizzes not found'
            ])->setStatusCode(404, 'Quizzes not found');
        }

        return response()->json([
            'status' => true,
            'message' => 'Quizzes found',
            'quizzes' => new QuizCollection($quizzes)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request_data = $request->only(['title', 'description', 'image']);
        $token = $request->bearerToken();

        $user = User::where('token', $token)->first();

        if (is_null($user)) {
            return response()->json([
                'status' => false,
                'message' => 'Quiz not store'
            ])->setStatusCode(404, 'Quiz not found');
        }
        
        $quiz = Quiz::create([
            'title' => $request_data['title'],
            'description' => $request_data['description'],
            'user_id' => $user->id
        ]);

        if (is_null($quiz)) {
            return response()->json([
                'status' => false,
                'message' => 'Quiz not store'
            ])->setStatusCode(404, 'Quiz not found');
        }
        
        $question = Question::create([
            'question' => 'Вопрос', 
            'score' => 0,
            'type' => 'radio',
            'quiz_id' => $quiz->id
        ]);
        
        if (is_null($question)) {
            return response()->json([
                'status' => false,
                'message' => 'Quiz not store'
                ])->setStatusCode(404, 'Quiz not store');
            }
            
            
        $variant = Variant::create([
            'answer' => 'Вариант 1',
            'correct' => 0,
            'question_id' => $question->id
        ]);

        if (is_null($variant)) {
            return response()->json([
                'status' => false,
                'message' => 'Quiz not store'
                ])->setStatusCode(404, 'Quiz not store');
            }
            
            
        $dataUploadedImage = $this->uploadImageInCatalog($request);
        
        if ($dataUploadedImage['status']) {
            $quiz->image = $dataUploadedImage['file_path'];
            $quiz->save();
        }

        return response()->json([
            'status' => true,
            'message' => 'Quiz store',
            'quiz' => new QuizResource($quiz)
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json([
            'quiz' => Quiz::find($id),
            'questions' => new QuestionCollection(Question::where('quiz_id', $id)->orderBy('position', 'asc')->get())
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request_data = $request->only(['title', 'description', 'user_id']);
        $quiz = Quiz::find($id);

        if(!$quiz) {
            return response()->json([
                'status' => false,
                'message' => 'Quiz not found'
            ])->setStatusCode(404, 'Quiz not found');
        }

        foreach($request_data as $key => $data) {
            $quiz->$key = $data;
        }

        $quiz->save();

        return response()->json([
            'status' => true,
            'message' => 'Quiz is updated',
            'quiz' => new QuizResource($quiz)
        ])->setStatusCode(200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $quiz = Quiz::find($id);

        if(!$quiz) {
            return response()->json([
                'status' => false,
                'message' => 'Quiz not found'
            ])->setStatusCode(404, 'Quiz not found');
        }
        
        if($this->removeImageInCatalog($quiz)['status']) {
            $quiz->delete();
    
            return response()->json([
                'status' => true,
                'message' => 'Quiz is deleted'
            ])->setStatusCode(200);
        }

        return response()->json([
            'status' => false,
            'message' => "Quiz is not deleted"
        ])->setStatusCode(404);

    }

    public function updateQuiz(Request $request, $id) {
        $request_data = $request->only(['title', 'description', 'user_id', 'image']);
        $quiz = Quiz::find($id);

        if(!$quiz) {
            return response()->json([
                'status' => false,
                'message' => 'Quiz not found'
            ])->setStatusCode(404, 'Quiz not found');
        }

        foreach($request_data as $key => $data) {
            if ($key === 'image') {
                $dataUploadedImage = $this->uploadImageInCatalog($request);
        
                if ($dataUploadedImage['status']) {
                    $quiz->image = $dataUploadedImage['file_path'];
                }
            }
            else {
                $quiz->$key = $data;
            }
        }

        $quiz->save();

        return response()->json([
            'status' => true,
            'message' => 'Quiz is updated',
            'quiz' => new QuizResource($quiz)
        ])->setStatusCode(200);
    }

    public function uploadImageInCatalog(Request $request) {
        try {
            if($request->hasFile('image')) {
                $file = $request->file('image');
                $file_name = uniqid() . '.' . $file->getClientOriginalName();
                $catalog = 'images';
                $file->move(public_path($catalog), $file_name);
                $file_path = '/' . $catalog . '/' . $file_name;

                return [
                    'status' => true,
                    'message' => 'File uploaded seccessfully',
                    'file_path' => $file_path
                ];
            }

            return [
                'status' => false,
                'message' => 'File not uploaded seccessfully'
            ];

        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function removeImageInDataBase($id) {
        $quiz = Quiz::find($id);

        if (!$quiz) {
            return response()->json([
                'status' => false,
                'message' => 'Quiz not found'
            ])->setStatusCode(404, 'Quiz not found');
        }

        if (!is_null($quiz->image)) {
            $dataUploadedImage = $this->removeImageInCatalog($quiz);
        
            if ($dataUploadedImage['status']) {
                $quiz->image = null;
                return response()->json([
                    'status' => true,
                    'message' => 'File is remove'
                ]);
            }

            return response()->json($dataUploadedImage)->setStatusCode(404, 'File not remove seccessfully');
        }
        
        return response()->json([
            'status' => false,
            'message' => 'File not sended'
        ])->setStatusCode(404, 'File not sended');
    }

    public function removeImageInCatalog($quiz) {
        try {
            if(($quiz->image) && file_exists(public_path($quiz->image))) {
                if(unlink(public_path(($quiz->image)))) {
                    return [
                        'status' => true,
                        'message' => 'File remove seccessfully'
                    ];
                }
                else {
                    return [
                        'status' => false,
                        'message' => 'File not remove seccessfully'
                    ];
                }
            }

            return [
                'status' => true,
                'message' => 'File not found'
            ];

        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
