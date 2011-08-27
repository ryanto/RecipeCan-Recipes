require 'fileutils'
svn_repo = "http://plugins.svn.wordpress.org/recipecan-recipes"

task :default do
end

desc "copy files into local svn dir and push up to repo"
task :build do
  files = Dir.glob("**/*")
  files.delete('Rakefile')

  svn_dir = File.join(File.absolute_path('./'), '..', 'svn', 'recipecan-recipes', 'trunk')
  
  # i think we should empty out svn dir
  
  files.each do |file|
    # puts "#{file} -> #{svn_dir}/#{file}"
    if File.directory?(file)
      mkdir_p("#{svn_dir}/#{file}")
    else
      cp("./#{file}", "#{svn_dir}/#{file}")
    end
  end

  # need an add script that does svn status and looks for new files
  #sh "cd ../svn/recipecan-recipes/ && svn add "

  sh "cd ../svn/recipecan-recipes/ && svn ci --username ryanto -m 'commit files, see git for history'"
end

desc "create a tag based off the current version"
task :tag do

  stable_tag = nil

  # find version
  File.open(File.join('readme.txt')).each do |line|
    if line =~ /^Stable tag: (.*)/
      stable_tag = line.gsub(/Stable tag: /, '').strip
    end
  end

  # make sure version tag doesnt exist
  tag_path = File.join(File.absolute_path('./'), '..', 'svn', 'recipecan-recipes', 'tags', stable_tag)
  if File.directory?(tag_path)
    puts "Tag #{stable_tag} already exists, bump version"
    exit
  end

  # svn copy
  sh "cd ../svn/recipecan-recipes/ && svn copy #{svn_repo}/trunk #{svn_repo}/tags/#{stable_tag} --username ryanto -m 'Releasing version #{stable_tag}'"
  
  #update svn repo
  sh "cd ../svn/recipecan-recipes/ && svn up"

end

desc "start sass watch"
task :sass do
  sh 'sass --watch ./stylesheets'
end
