module Entity
    # Represent a release by a musician or band.
    class Album < Entity::Slugged
        include Entity::Thumbnailed

        self.abstract_class = true

        def release_date_formated
            self.release_date
        end

        def title_context
            [self.title, self.artist.name]
        end

        def meta_title
            self.title_context
        end

        def meta_keywords
            self.title_context + [ t("album review") ]
        end

        def meta_description
            str = t "View the reviewing statistics of %s, an album by %s that was released %s."
            sprintf(str, self.title, self.artist.name, self.release_date)
        end

    end
end
