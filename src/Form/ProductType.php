<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom'
            ])
            ->add('price', TextType::class, [
                'label' => 'Prix'
            ])
            ->add('image', FileType::class, [
                'mapped' => false, // Important : ne pas lier à l'entité directement
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Merci d\'uploader une image valide (JPEG ou PNG)',
                    ])
                ],
            ])
            ->add('stock_xs', IntegerType::class, ['mapped' => false, 'label' => 'Stock XS'])
            ->add('stock_s', IntegerType::class, ['mapped' => false, 'label' => 'Stock S'])
            ->add('stock_m', IntegerType::class, ['mapped' => false, 'label' => 'Stock M'])
            ->add('stock_l', IntegerType::class, ['mapped' => false, 'label' => 'Stock L'])
            ->add('stock_xl', IntegerType::class, ['mapped' => false, 'label' => 'Stock XL']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
