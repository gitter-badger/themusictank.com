Rails.application.routes.draw do

  # Facebook auth rules.
  get 'auth/:provider/callback', to: 'sessions#create'
  get 'sessions/signout', to: 'sessions#destroy', as: 'signout'
  get 'sessions/login', to: 'sessions#login', as: :login_url

  # Routes that submit things
  post 'ajax/reviewer', :to => 'ajax#reviewer', via: [:post]
  patch 'profiles/update', :to => 'profiles#update'
  match 'ajax/bugreport', :to => 'ajax#bugreport', via: [:get, :post]

  # General fallback.
  get ':controller(/:action(/:slug))'

  root 'tmt#homepage'

  #constraints(AuthConstraint.new) do
  #  root :to => 'profiles#dashboard', as: :authenticated_root
  #end

  # The priority is based upon order of creation: first created -> highest priority.
  # See how all your routes lay out with "rake routes".

  # You can have the root of your site routed with "root"
  # root 'welcome#index'

  # Example of regular route:
  #   get 'products/:id' => 'catalog#view'

  # Example of named route that can be invoked with purchase_url(id: product.id)
  #   get 'products/:id/purchase' => 'catalog#purchase', as: :purchase

  # Example resource route (maps HTTP verbs to controller actions automatically):
  #   resources :products

  # Example resource route with options:
  #   resources :products do
  #     member do
  #       get 'short'
  #       post 'toggle'
  #     end
  #
  #     collection do
  #       get 'sold'
  #     end
  #   end

  # Example resource route with sub-resources:
  #   resources :products do
  #     resources :comments, :sales
  #     resource :seller
  #   end

  # Example resource route with more complex sub-resources:
  #   resources :products do
  #     resources :comments
  #     resources :sales do
  #       get 'recent', on: :collection
  #     end
  #   end

  # Example resource route with concerns:
  #   concern :toggleable do
  #     post 'toggle'
  #   end
  #   resources :posts, concerns: :toggleable
  #   resources :photos, concerns: :toggleable

  # Example resource route within a namespace:
  #   namespace :admin do
  #     # Directs /admin/products/* to Admin::ProductsController
  #     # (app/controllers/admin/products_controller.rb)
  #     resources :products
  #   end
end
