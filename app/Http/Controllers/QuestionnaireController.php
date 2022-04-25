<?php

namespace App\Http\Controllers;

use App\Answer;
use App\AuditTrail;
use App\FollowupQuestion;
use App\FollowupResponse;
use App\Question;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class QuestionnaireController extends Controller
{
    public  function questions()
    {
        $questions = Question::orderBy('id', 'desc')->get();

        return view('questionnaires.questions')->with([
            'questions' => $questions,
        ]);
    }
    public function questionsDT() {


        $questions = Question::orderBy('id', 'desc')->get();


        return DataTables::of($questions)

            ->addColumn('answers', function($question) {
                return count($question->answers);
            })
            ->addColumn('responses', function($question) {
                return count($question->responses);
            })
            ->addColumn('actions', function($question){ // add custom column
                $actions = '<div class="pull-right">
                        <button class="btn btn-primary btn-sm add-answer-btn" acs-id="'.$question->id .'" que-id="'.$question->question .'">
                    <i class="fa fa-plus"></i> Add Answer</button>';

                $actions .= '<a class="btn btn-success btn-sm" href="'.url('questions',$question->id) .'">
                    <i class="fa fa-info"></i> Details</a>';

                $actions .= '<button source="' . route('edit-question' ,  $question->id) . '"
                    class="btn btn-warning btn-sm edit-question-btn" acs-id="'.$question->id .'">
                    <i class="fa fa-edit">edit</i> Edit</button>';


                $actions .= '<form action="'. route('delete-question',  $question->id) .'" style="display: inline;" method="post" class="del_question_form">';
                $actions .= method_field('DELETE');
                $actions .= csrf_field() .' <button class="btn btn-danger btn-sm">Delete</button></form>';
                $actions .= '</div>';
                return $actions;
            })
            ->rawColumns(['actions'])
            ->make(true);
    }
    public function add_question(Request $request)
    {

        $this->validate($request, [
            'question' =>'required',
            'type' =>'required',
            'answer_count' =>'required',
        ]);

        $question = new Question();
        $question->question = $request->question;
        $question->answer_count = $request->answer_count;
        $question->type = $request->type;
        $question->saveOrFail();

        request()->session()->flash('success', 'Question has been created.');

        return redirect()->back();
    }
    public function delete_question($id)
    {
        try {
            Question::destroy($id);

            request()->session()->flash('success', 'Question has been deleted.');
        } catch (QueryException $qe) {
            request()->session()->flash('warning', 'Could not delete question because it\'s being used in the system!');
        }

        return redirect()->back();
    }

    public function edit_question($id)
    {
        $question = Question::find($id);
        return $question;
    }

    public function update_question(Request $request)
    {

        $data = request()->validate([
            'question' => 'required|unique:questions,question,'.$request->id,
            'type'  => 'required',
            'answer_count'  => 'required',
            'id' => 'required|exists:questions,id',
        ]);



        $question = Question::find($request->id);
        $question->question = $request->question;
        $question->type = $request->type;
        $question->answer_count = $request->answer_count;
        $question->update();

        AuditTrail::create([
            'created_by' => auth()->user()->id,
            'action' => 'Updated question #'.$request->id.' ('.$request->question.' )',
        ]);

        request()->session()->flash('success', 'Question has been updated.');
        return redirect()->back();
    }


    public  function question_details($id)
    {
        $question = Question::find($id);
        if (is_null($question))
            abort(404);

        return view('questionnaires.question_details')->with([
            'question' => $question,
        ]);
    }
    public function answersDT($questionId) {


        $answers = Answer::where('question_id',$questionId)->orderBy('id', 'desc')->get();


        return DataTables::of($answers)

            ->addColumn('followup', function($answers) {
                return optional($answers->followup)->question;
            })

            ->addColumn('actions', function($answers){ // add custom column

                $actions = '<div class="pull-right">
                    <button class="btn btn-primary btn-sm add-followup-btn" acs-id="'.$answers->id .'" ans-id="'.$answers->answer .'">
                    <i class="fa fa-plus"></i> Add Follow up</button>

                      ';
                $actions .= '<form action="'. route('delete-answer',  $answers->id) .'" style="display: inline;" method="post" class="del_answer_form">';
                $actions .= method_field('DELETE');
                $actions .= csrf_field() .' <button class="btn btn-danger btn-sm">Delete</button></form>';
                $actions .= '</div>';
                return $actions;
            })
            ->rawColumns(['actions'])
            ->make(true);
    }
    public function add_answer(Request $request)
    {

        $this->validate($request, [
            'question_id' => 'required',
            'answer' => 'required',
        ]);

        $questionAnswer = new Answer();
        $questionAnswer->question_id = $request->question_id;
        $questionAnswer->answer = $request->answer;
        $questionAnswer->saveOrFail();

        request()->session()->flash('success', 'Answer has been created.');

        return redirect()->back();
    }
    public function add_followup(Request $request)
    {

        $this->validate($request, [
            'answer_id' => 'required',
            'followup' => 'required',
        ]);

        $exists = FollowupQuestion::where('answer_id',$request->answer_id)->first();

        if (is_null($exists)){

            $followUp = new FollowupQuestion();
            $followUp->answer_id = $request->answer_id;
            $followUp->question = $request->followup;
            $followUp->saveOrFail();

            request()->session()->flash('success', 'Follow up question has been created.');
        }else{
            request()->session()->flash('warning', 'A follow up question for this answer already exists');
        }

        return redirect()->back();
    }
    public function delete_answer($id)
    {
        try {

            foreach (FollowupQuestion::where('answer_id',$id)->get() as $flUpQ){
                FollowupResponse::where('followup_question_id',$flUpQ->id)->delete();
                $flUpQ->delete();
            }

            Answer::destroy($id);

            request()->session()->flash('success', 'Answer has been deleted.');
        } catch (QueryException $qe) {
            request()->session()->flash('warning', 'Could not delete answer because it\'s being used in the system!');
        }

        return redirect()->back();
    }



}
