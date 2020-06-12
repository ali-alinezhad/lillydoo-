<?php


namespace App\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\File;

class AddressBookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('firstName', TextType::class, ['label' => 'First Name'])
        ->add('lastName', TextType::class, ['label' => 'Last Name'])
        ->add('street', TextType::class)
        ->add('zip', IntegerType::class, ['label' => 'Zip Cod'])
        ->add('city', TextType::class)
        ->add('country', TextType::class)
        ->add('phone', IntegerType::class, ['label' => 'Phone Number'])
        ->add('birthday', DateType::class)
        ->add('email', EmailType::class)
        ->add('picture', FileType::class, [
            'required'=>false,
            'data_class' => null,
            'constraints' => [
                new File([
                    'maxSize' => '2048k',
                    'mimeTypes' => [
                        'image/*',
                    ],
                    'mimeTypesMessage' => 'Please upload a valid image',
                ])
              ],
            ])
        ->add('save', SubmitType::class)
        ->getForm();
    }
}
