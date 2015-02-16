MusicBrainz.configure do |c|
  c.app_name = "The Music Tank"
  c.app_version = "1.0"
  c.contact = "frank@francoisfaubert.com"

  c.cache_path = "/tmp/musicbrainz-cache"
  c.perform_caching = true

  c.query_interval = 1.2 # seconds
  c.tries_limit = 2
end
