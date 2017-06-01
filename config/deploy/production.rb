set :environment,        'production'
set :domain,             'gunnertechnetwork.herokuapp.com'


# GitHub settings #######################################################################################
set :branch, "production"
#########################################################################################################

after "plugin:assets:deploy", "assets:deploy"
after "site:assets:deploy", "assets:deploy"

namespace :assets do
  task :deploy, :roles => :app do
    system "git add .; git commit -am 'Asset Version Bump'; git push origin #{branch};"
  end
end