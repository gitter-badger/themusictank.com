module Entity

    # Represent a release by a musician or band.
    class Artist < Entity::Slugged
        include Entity::Thumbnailed
        include Entity::Oembedable

        self.abstract_class = true

        def title_context
            [self.name]
        end

        def meta_title
            self.title_context
        end

        def meta_keywords
            self.title_context + [ t("artist review") ]
        end

        def meta_description
            str = "View the reviewing statistics of %s."
            sprintf(str, self.name)
        end

    end
end
