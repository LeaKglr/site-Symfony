<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'attr' => ['class' => 'form-control']
            ])
            ->add('price', TextType::class, [
                'label' => 'Prix',
                'attr' => ['class' => 'form-control', 'step' => '0.01']
            ])
            ->add('stock_xs', IntegerType::class, ['mapped' => false, 'label' => 'Stock XS'])
            ->add('stock_s', IntegerType::class, ['mapped' => false, 'label' => 'Stock S'])
            ->add('stock_m', IntegerType::class, ['mapped' => false, 'label' => 'Stock M'])
            ->add('stock_l', IntegerType::class, ['mapped' => false, 'label' => 'Stock L'])
            ->add('stock_xl', IntegerType::class, ['mapped' => false, 'label' => 'Stock XL']);
            
            if (!$options['is_edit']) {
                $builder->add('imageFile', FileType::class, [
                    'mapped' => false,
                    'required' => false,
                    'label' => 'Choisir une image',
                    'constraints' => [
                        new File([
                            'maxSize' => '2M',
                            'mimeTypes' => [
                                'image/jpeg',
                                'image/png',
                                'image/jpg',
                            ],
                            'mimeTypesMessage' => 'Merci d\'uploader une image valide (JPEG ou PNG)',
                        ])
                    ],
                ]);
            };
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
            'is_edit' => false,
        ]);
    }
}
