                                <p class="h4 inline flx-cntnr flx-row flx-nowrap flx-start">
                                    <span><strong>Language:&nbsp;</strong></span><span>{{ main.getTypeProperty('codeLanguages')[question.getQuestionLanguage()] }}</span>
                                </p>
                                <p class="h4 inline flx-cntnr flx-row flx-nowrap flx-start">
                                    <span><strong>Input: </strong></span><p>{{ question.getQuestionInputTest() }}</p>
                                </p>
                                <p class="h4 inline flx-cntnr flx-row flx-nowrap flx-start">
                                    <span><strong>Output: </strong></span><p>{{ question.getQuestionOutputTest() }}</p>
                                </p>
