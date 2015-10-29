## Documenting

rake doc:app
gem server
http://0.0.0.0:8808


## Assets pipeline

heroku config:set BUILDPACK_URL='git://github.com/qnyp/heroku-buildpack-ruby-bower.git#run-bower'

## Necessary configuration values

* LastFm_key
* Youtube_key
* Github_username
* Github_password
* Github_repo
* Facebook_key
* Facebook_secret
* Amazon_S3_bucket
* Amazon_S3_key
* Amazon_S3_secret
