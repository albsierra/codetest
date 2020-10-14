                                <p class="h4 inline flx-cntnr flx-row flx-nowrap flx-start">
                                    <span><strong>Language:&nbsp;</strong></span><span>{{ main.getTypeProperty('codeLanguages')[question.getQuestionLanguage()].name }}</span>
                                </p>
                                <p class="h4 inline flx-cntnr flx-row flx-nowrap flx-start">
                                    <span><strong>Input: </strong></span><pre>{{ question.getQuestionInputTest() }}</pre>
                                </p>
                                <p class="h4 inline flx-cntnr flx-row flx-nowrap flx-start">
                                    <span><strong>Output: </strong></span><pre>{{ question.getQuestionOutputTest() }}</pre>
                                </p>
