module Entity
    # Allows TMT objects to be included in social media
    module Oembedable
        def to_oembed_hash
            hash = {
                "version"       => "1.0",
                "type"          => "rich",
                "provider_name" => "The Music Tank",
                "provider_url"  => "http://www.themusictank.com/",
                "width"         => 1280,
                "height"        => 720,
                "title"         => self.meta_title.join(", "),
                # "author_name": "",
                # "author_url": "http://www.themusictank.com/profiles/francoisfaubert",
                "html"          => "<iframe width=\"1280\" height=\"720\" src=\"" + link_to_me + "\"></iframe>",
            }
        end

        private

        def link_to_me
            "http://www.themusictank.com/embed/#{self.class.name.demodulize.downcase}s/#{self.slug}"
        end

    end
end
