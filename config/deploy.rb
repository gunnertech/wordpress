
require 'erb'
require 'open-uri'
require 'rubygems'
require 'route53'
require 'socket'
require 'aws-sdk'
require 'fileutils'

set :stages, %w(production)
set :default_stage, "production"
set :application, "gunnertechnetwork"
set :keep_releases, 3

require 'capistrano/ext/multistage'

set(:port) { "22" }
set(:use_sudo) { false }
 
# GitHub settings #######################################################################################
default_run_options[:pty] = true
ssh_options[:forward_agent] = true
set :repository, "git@github.com:gunnertech/wordpress.git"
set :scm, "git"
set :scm_verbose, true
set :repository_cache, "git_cache"
set :deploy_via, :remote_cache
#########################################################################################################

set(:user) { "doesnotmatter" }
set(:home_dir) { "/home" }
set(:deploy_to) { "#{home_dir}/#{user}/#{application}" }
set(:user_dir) { "#{home_dir}/#{user}" }

set(:secret_keys) { open('http://api.wordpress.org/secret-key/1.1/').read }

role(:app) { domain }
role(:web) { domain }
role(:db, :primary => true) { domain }


def send_assets_to_s3(path, site_name)
  remote_path = path.gsub(/^vendor\/WordPress\/WordPress\//,"").gsub(/^public\//,"")

  puts "~~~~~#{path}"


  system "find #{path} -iname '*.js' -exec config/scripts/do_gzip {} \\;"
  system "find #{path} -iname '*.css' -exec config/scripts/do_gzip {} \\;"
  system "s3cmd sync  --progress --acl-public --add-header 'Expires: Thu, 01 Dec 2020 16:00:00 GMT' --add-header 'Cache-Control: max-age=94608000' #{path}  s3://gunnertechnetwork/#{remote_path} --exclude '*.*' --include '*.js' --include '*.css' --include '*.jpg' --include '*.jpeg' --include '*.png' --include '*.gif'; true"
  system "s3cmd sync  --progress --acl-public --add-header 'Expires: Thu, 01 Dec 2020 16:00:00 GMT' --add-header 'Cache-Control: max-age=94608000' --mime-type='application/javascript' --add-header 'Content-Encoding:gzip' #{path}  s3://gunnertechnetwork/#{remote_path} --exclude '*.*' --include '*.js.gzip'; true"
  system "s3cmd sync  --progress --acl-public --add-header 'Expires: Thu, 01 Dec 2020 16:00:00 GMT' --add-header 'Cache-Control: max-age=94608000' --mime-type='text/css' --add-header 'Content-Encoding:gzip' #{path}  s3://gunnertechnetwork/#{remote_path} --exclude '*.*' --include '*.css.gzip'; true"
  
  system "s3cmd sync  --progress --acl-public --add-header 'Expires: Thu, 01 Dec 2020 16:00:00 GMT' --add-header 'Cache-Control: max-age=94608000' --mime-type='application/javascript' #{path}  s3://gunnertechnetwork/#{remote_path} --exclude '*.*' --include '*.js'; true"
  system "s3cmd sync  --progress --acl-public --add-header 'Expires: Thu, 01 Dec 2020 16:00:00 GMT' --add-header 'Cache-Control: max-age=94608000' --mime-type='text/css' #{path}  s3://gunnertechnetwork/#{remote_path} --exclude '*.*' --include '*.css'; true"
  
  system "s3cmd sync  --progress --acl-public --add-header 'Expires: Thu, 01 Dec 2020 16:00:00 GMT' --add-header 'Cache-Control: max-age=94608000' --mime-type='image/png' #{path}  s3://gunnertechnetwork/#{remote_path} --exclude '*.*' --include '*.png'; true"
  system "s3cmd sync  --progress --acl-public --add-header 'Expires: Thu, 01 Dec 2020 16:00:00 GMT' --add-header 'Cache-Control: max-age=94608000' --mime-type='image/jpeg' #{path}  s3://gunnertechnetwork/#{remote_path} --exclude '*.*' --include '*.jpg'; true"
  system "s3cmd sync  --progress --acl-public --add-header 'Expires: Thu, 01 Dec 2020 16:00:00 GMT' --add-header 'Cache-Control: max-age=94608000' --mime-type='image/jpeg' #{path}  s3://gunnertechnetwork/#{remote_path} --exclude '*.*' --include '*.jpeg'; true"
  system "s3cmd sync  --progress --acl-public --add-header 'Expires: Thu, 01 Dec 2020 16:00:00 GMT' --add-header 'Cache-Control: max-age=94608000' --mime-type='image/gif' #{path}  s3://gunnertechnetwork/#{remote_path} --exclude '*.*' --include '*.gif'; true"
  system "s3cmd sync  --progress --acl-public --add-header 'Expires: Thu, 01 Dec 2020 16:00:00 GMT' --add-header 'Cache-Control: max-age=94608000' --mime-type='application/x-font-woff' #{path}  s3://gunnertechnetwork/#{remote_path} --exclude '*.*' --include '*.woff'; true"
  system "s3cmd sync  --progress --acl-public --add-header 'Expires: Thu, 01 Dec 2020 16:00:00 GMT' --add-header 'Cache-Control: max-age=94608000' --mime-type='application/x-font-eot' #{path}  s3://gunnertechnetwork/#{remote_path} --exclude '*.*' --include '*.eot'; true"
  # system "s3cmd sync  --progress --acl-public --add-header 'Expires: Thu, 01 Dec 2020 16:00:00 GMT' --add-header 'Cache-Control: max-age=94608000' --mime-type='application/x-font-svg' #{path}  s3://gunnertechnetwork/#{remote_path} --exclude '*.*' --include '*.svg'; true"
  system "s3cmd sync  --progress --acl-public --add-header 'Expires: Thu, 01 Dec 2020 16:00:00 GMT' --add-header 'Cache-Control: max-age=94608000' --mime-type='image/svg+xml' #{path}  s3://gunnertechnetwork/#{remote_path} --exclude '*.*' --include '*.svg'; true"
  system "s3cmd sync  --progress --acl-public --add-header 'Expires: Thu, 01 Dec 2020 16:00:00 GMT' --add-header 'Cache-Control: max-age=94608000' --mime-type='application/x-font-ttf' #{path}  s3://gunnertechnetwork/#{remote_path} --exclude '*.*' --include '*.ttf'; true"
  
  # system "find #{path} -iname '*.gzip' -exec rm {} \\;true"
  
  file = "public/wp-config/assets.php"
  text = File.open(file).read

  File.open(file, 'w') do |out|
    puts text

    match = text.match(/define\("GT_ASSET_VERSION", "(\d+)"\);/)
    puts match[1].inspect
    out << text.gsub(/define\("GT_ASSET_VERSION", "\d+"\);/, 'define("GT_ASSET_VERSION", "'+(match[1].to_i+1).to_s+'");')
  end
end

namespace :all do
  namespace :assets do
    task :deploy, :roles => :app do
      hours = 1
      last_deploy = Time.parse('2016-11-02 17:14:19 -0500')
      dirs = []
      
      # dirs.push("wp-includes/") if File.mtime("wp-includes") >= hours.hour.ago
      # dirs.push("wp-admin/") if File.mtime("wp-admin") >= hours.hour.ago
      
      Dir.entries('public/wp-content/themes').select {|entry| File.directory? File.join('public/wp-content/themes',entry) and !(entry =='.' || entry == '..') }.each do |theme|
        path = "public/wp-content/themes/#{theme}/"
        if File.mtime(path) >= last_deploy
          puts "ADDING #{path}"
          dirs.push(path) 
        end
        # 
        # send_assets_to_s3(path, theme)  
      end
      
      Dir.entries('public/wp-content/plugins').select {|entry| File.directory? File.join('public/wp-content/plugins',entry) and !(entry =='.' || entry == '..') }.each do |plugin|
        path = "public/wp-content/plugins/#{plugin}/"
        if File.mtime(path) >= last_deploy
          puts "ADDING #{path}"
          dirs.push(path) 
        end
      end

      Dir.entries('vendor/WordPress/WordPress/wp-content/themes').select {|entry| File.directory? File.join('vendor/WordPress/WordPress/wp-content/themes',entry) and !(entry =='.' || entry == '..') }.each do |theme|
        path = "vendor/WordPress/WordPress/wp-content/themes/#{theme}/"
        if File.mtime(path) >= last_deploy
          puts "ADDING #{path}"
          dirs.push(path) 
        end
        # 
        # send_assets_to_s3(path, theme)  
      end
      
      Dir.entries('vendor/WordPress/WordPress/wp-content/plugins').select {|entry| File.directory? File.join('vendor/WordPress/WordPress/wp-content/plugins',entry) and !(entry =='.' || entry == '..') }.each do |plugin|
        path = "vendor/WordPress/WordPress/wp-content/plugins/#{plugin}/"
        if File.mtime(path) >= last_deploy
          puts "ADDING #{path}"
          dirs.push(path) 
        end
      end
      
      dirs.each do |dir|
        send_assets_to_s3(dir, nil)
      end
      
      # site_name = 'wp-includes'
      # path = "wp-includes/"
      # 
      # send_assets_to_s3(path, site_name)
      # 
      # site_name = 'wp-admin'
      # path = "wp-admin/"
      
      # send_assets_to_s3(path, site_name)
      
    end
  end
end


namespace :theme do
  namespace :assets do
    task :deploy, :roles => :app do
      site_name = Capistrano::CLI.ui.ask("Please type the name to the theme: ")
      path = "public/wp-content/themes/#{site_name}/"
      
      send_assets_to_s3(path, site_name)

      path = "vendor/WordPress/WordPress/wp-content/themes/#{site_name}/"
      
      send_assets_to_s3(path, site_name)
    end
  end
end

namespace :plugin do
  namespace :assets do
    task :deploy, :roles => :app do
      site_name = Capistrano::CLI.ui.ask("Please type the name of the plugin: ")
      path = "public/wp-content/plugins/#{site_name}/"
      
      send_assets_to_s3(path, site_name)

      path = "vendor/WordPress/WordPress/wp-content/plugins/#{site_name}/"
      
      send_assets_to_s3(path, site_name)
    end
  end
end


namespace :wordpress do
  namespace :assets do
    task :deploy, :roles => :app do
      site_name = 'wp-includes'
      path = "vendor/WordPress/WordPress/wp-includes/"
      
      send_assets_to_s3(path, site_name)
    end
  end
end

namespace :wp_admin do
  namespace :assets do
    task :deploy, :roles => :app do
      site_name = 'wp-admin'
      path = "vendor/WordPress/WordPress/wp-admin/"
      
      send_assets_to_s3(path, site_name)
    end
  end
end



namespace :assets do
  task :deploy, :roles => :app do
  end
end

