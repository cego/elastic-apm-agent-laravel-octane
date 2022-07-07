# elastic-apm-agent-laravel-octane
A Laravel package for elastic apm agent running on octane
[![Laravel Octane Compatible](https://img.shields.io/badge/Laravel%20Octane-Compatible-success?style=flat&logo=laravel)](https://github.com/laravel/octane)

## Install
`composer require cego/elastic-apm-agent-laravel-octane`

If you are having issues with transactions not being sent to the APM server, please try disabling async APM backend, for example by setting the following env:

`ELASTIC_APM_ASYNC_BACKEND_COMM=false`


## Contributing
You are welcome to open issues and pull request, please describe the issues and pull requests carefully to enable our understanding of the issue.

Before contributing code, please run the cs-fixer:
```/vendor/bin/php-cs-fixer fix```