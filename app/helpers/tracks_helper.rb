module TracksHelper

    def self.isSameSource album1, albums2
        unless album1.nil? || albums2.nil?
            return album1.slug.eql? albums2.slug
        end
        false
    end

end
