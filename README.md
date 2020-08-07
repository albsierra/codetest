# codetest
A simple Tsugi tool to prompt users to respond to short answer questions.

## TO CONVERT THE SCHEMA FROM THE OLD CODE TEST:
### ct_main
* Rename SetID to ct_id and update primary key
* Rename UserID to user_id and make not null
* Make context_id not null
* Make link_id not null
* Rename Modified to modified

### ct_questions -> ct_question
* Move all data from ct_questions to ct_question into correct columns with new names

### ct_answer
* Rename AnswerID to answer_id
* Rename UserID to user_id and make not null
* Remove SetID
* Rename QID to question_id and make not null
* Rename Answer to answer_txt
* Rename Modified to modified
