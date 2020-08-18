{% extends "mainTemplate.php" %}
{% block pageTitle %}
{{ OUTPUT.pageTitle('Download Results', true, false) }}
{% endblock %}
{% block content %}
<p class="lead">Click on the link below to download the student results.</p>
<h4>
    <a href="actions/ExportToFile.php">
        <span class="fa fa-download" aria-hidden="true"></span> CodeTest-{{ CONTEXT.title }}-Results.xls
    </a>
</h4>

{{ OUTPUT.helpModal("Code Test Help", help) }}

{% endblock %}