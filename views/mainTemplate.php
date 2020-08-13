{% block preHeader %}{% endblock %}
{{ OUTPUT.header() }}
{% block postHeader %}{% endblock %}
{{ include('dao/tool-header.html') }}
{% block postToolHeader %}{% endblock %}
{{ OUTPUT.bodyStart() }}
{% block preMenu %}{% endblock %}

{{ OUTPUT.topNav(menu) }}

    {% block postMenu %}{% endblock %}

    <div class="container-fluid">
        {{ OUTPUT.flashMessages() }}
        {% block pageTitle %}{% endblock %}
        {% block content %} {% endblock %}
    </div> <!-- End container -->
{{ OUTPUT.footerStart() }}
{% block footer %} {% endblock %}
{{ include('dao/tool-footer.html') }}
{{ OUTPUT.footerEnd() }}
